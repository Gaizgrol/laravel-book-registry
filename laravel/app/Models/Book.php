<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function indices()
    {
        return $this->belongsToMany(Index::class, 'books_indexes', 'book_id', 'index_id')->with('subIndices');
    }
}
