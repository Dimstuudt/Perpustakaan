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
        'type', 
        'category_id',
        'description',
        'file_path',
        'cover_path',
        'stock'
    ];

    // ✅ relasi ke category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getCoverUrlAttribute()
{
    return $this->cover_path
        ? asset('storage/'.$this->cover_path)
        : null;
}

    public function getFileUrlAttribute()
{
    return $this->file_path
        ? asset('storage/'.$this->file_path)
        : null;
}

    public function isEbook()
    {
        return $this->type === 'ebook';
    }

    public function isPhysical()
    {
        return $this->type === 'physical';
    }

    public function loans()
{
    return $this->hasMany(Loan::class);
}


}
