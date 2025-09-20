<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ✅ import SoftDeletes

class Book extends Model
{
    use HasFactory, SoftDeletes; // ✅ tambahkan SoftDeletes

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

    // ✅ relasi ke category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
