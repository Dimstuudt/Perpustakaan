<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    protected $fillable = ['name','code','description'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function cabinet()
{
    return $this->belongsTo(Cabinet::class, 'cabinet_id');
}

}
