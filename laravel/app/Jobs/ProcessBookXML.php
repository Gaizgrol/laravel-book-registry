<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use XMLWriter;

class ProcessBookXML implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $book)
    {
    }

    private function writeTags(XMLWriter $xw, array $indices)
    {
        foreach ($indices as $index) {
            xmlwriter_start_element($xw, 'item');
            xmlwriter_start_attribute($xw, 'pagina');
            xmlwriter_text($xw, $index['page']);
            xmlwriter_end_attribute($xw);
            xmlwriter_start_attribute($xw, 'titulo');
            xmlwriter_text($xw, $index['title']);
            xmlwriter_end_attribute($xw);
            $this->writeTags($xw, $index['sub_indices']);
            xmlwriter_end_element($xw);
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 2);
        xmlwriter_set_indent_string($xw, ' ');
        xmlwriter_start_document($xw, '1.0', 'UTF-8');

        xmlwriter_start_element($xw, 'indice');
        $this->writeTags($xw, $this->book['indices']);
        xmlwriter_end_element($xw);

        xmlwriter_end_document($xw);

        $xml = xmlwriter_output_memory($xw);

        $uuid = (string) \Illuminate\Support\Str::uuid();
        $bookId = $this->book['id'];
        Storage::disk('local')->put("Book-{$bookId}-{$uuid}.xml", $xml);
    }
}
