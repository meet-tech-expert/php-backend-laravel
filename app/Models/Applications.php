<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applications extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = [
        'deleted_at'
    ];

    public function student()
    {
        return $this->belongsTo('App\Models\Students', 'student_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function internshipPost()
    {
        return $this->belongsTo('App\Models\InternshipPosts', 'internship_post_id', 'id');
    }
}
