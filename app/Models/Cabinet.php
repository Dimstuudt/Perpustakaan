<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cabinet extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description'];

    // One cabinet has many racks
    public function racks()
    {
        return $this->hasMany(Rack::class);
    }
}

