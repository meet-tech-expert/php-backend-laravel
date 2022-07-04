<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessIndustries extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function company()
    {
        return $this->hasMany('App\Models\Company', 'business_industry_id', 'id');
    }

    public function internship_posts()
    {
        return $this->hasMany('App\Models\InternshipPosts', 'industry_id');
    }
}
