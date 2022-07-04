<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaPosts extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = [
        'deleted_at'
    ];

    public function mediaViews()
    {
        return $this->hasMany(MediaPostView::class, 'media_post_id');
    }

    public function mediaTags()
    {
        return $this->belongsToMany(MediaTags::class, MediaPostTag::class, 'media_post_id', 'media_tag_id');
    }
}
