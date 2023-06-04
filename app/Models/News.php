<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    public function newsImage(){
        return $this->hasMany(NewsImage::class);
    }

    public function newsReactions(){
        return $this->hasMany(NewsReactions::class);
    }
}
