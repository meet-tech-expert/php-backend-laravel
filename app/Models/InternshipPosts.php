<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipPosts extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = [
        'deleted_at'
    ];

    public function workCategory()
    {
        return $this->belongsTo(WorkCategories::class, 'work_category_id');
    }

    // InternshipPosts have many Feedbacks
    public function feedbacks()
    {
        return $this->hasMany(Feedbacks::class, 'internship_post_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorites::class, 'internship_post_id');
    }

    public function applications()
    {
        return $this->hasMany(Applications::class, 'internship_post_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function internshipFeaturePosts()
    {
        return $this->belongsToMany(InternshipFeatures::class, InternshipFeaturePost::class, 'internship_post_id', 'internship_feature_id');
    }

}
