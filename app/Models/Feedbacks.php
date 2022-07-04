<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedbacks extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'company_id',
        'internship_post_id',
        'is_read',
        'super_power_review',
        'super_power_comment',
        'growth_idea_review',
        'growth_idea_comment',
        'posted_month'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function student()
    {
        return $this->belongsTo('App\Models\Students', 'student_id');
    }

    public function companies()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function internshipPost()
    {
        return $this->belongsTo('App\Models\InternshipPosts', 'internship_post_id');
    }

}
