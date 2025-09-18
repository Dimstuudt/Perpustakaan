<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'year',
        'pages',
        'category_id',
        'description',
        'file_path',
        'cover_path',
        
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
