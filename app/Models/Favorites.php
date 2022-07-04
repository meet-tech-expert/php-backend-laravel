<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorites extends Model
{
    use HasFactory;

    public function internship() {
        return $this->belongsTo(InternshipPosts::class, 'internship_post_id');
    }
}
