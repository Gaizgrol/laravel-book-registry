<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessBookXML;
use App\Models\Book;
use App\Models\BooksIndexesRelationship;
use App\Models\Index;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    private function validIndices(array $indices)
    {
        foreach ($indices as $index) {
            if (!is_array($index)) return false;
            if (!key_exists('titulo', $index) || !is_string($index['titulo'])) return false;
            if (!key_exists('pagina', $index) || !is_integer($index['pagina'])) return false;
            if (!key_exists('subindices', $index) || !is_array($index['subindices'])) return false;
            if (!$this->validIndices($index['subindices'])) return false;
        }
        return true;
    }

    private function saveIndices(array $indices, Index $parent = null)
    {
        $savedIndices = [];
        foreach ($indices as $index) {
            $newIndex = new Index();
            $newIndex->title = $index['titulo'];
            $newIndex->page = $index['pagina'];
            $newIndex->index_id = $parent?->id;
            $newIndex->save();
            $subIndexes = $this->saveIndices($index['subindices'], $newIndex);
            $newIndex = $newIndex->toArray();
            $newIndex['sub_indices'] = $subIndexes;
            $newIndex = (object) $newIndex;
            $savedIndices[] = $newIndex;
        }
        return $savedIndices;
    }

    public function index(Request $request)
    {
        $titulo = $request->query('titulo') ?? '';
        //$tituloDoIndice = $request->query('titulo_do_indice') ?? '';
        $books = array_map(
            function ($book) {
                return $book->toArray();
            },
            Book::where('title', 'ilike', "%{$titulo}%")
                ->with(['indices', 'author'])
                ->get()
                ->all()
        );
        return response()->json($books);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'titulo' => 'required',
            'indices' => 'array'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }
        if (!$this->validIndices($request->input('indices'))) {
            return response()->json([
                'error' => 'Indices does not have the following structure => Index: { titulo: string, pagina: int, subindices: Index[] }'
            ], 422);
        }
        DB::beginTransaction();
        try {
            $book = new Book();
            $book->title = $request->input('titulo');
            $book->user_id = $user['id'];
            $book->save();

            $indices = $this->saveIndices($request->input('indices'));

            foreach ($indices as $index) {
                $relation = new BooksIndexesRelationship();
                $relation->book_id = $book->id;
                $relation->index_id = $index->id;
                $relation->save();
            }

            $book = $book->toArray();
            $book['indices'] = $indices;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return response()->json($book);
    }

    public function queueJob(int $id)
    {
        $book = Book::where('id', $id)->with('indices')->get()->first();
        if (!$book) {
            return response()->json([
                'error' => 'Not found'
            ], 404);
        }
        ProcessBookXML::dispatch($book->toArray());
        return response()->json([
            'message' => 'Job queued.'
        ], 200);
    }
}
