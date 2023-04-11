<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Index extends Model
{
    use HasFactory;

    protected $table = 'indexes';
    public $timestamps = false;

    public function subIndices()
    {
        return $this->hasMany(self::class, 'index_id', 'id')->with('subIndices');
    }
}
