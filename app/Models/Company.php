<?php

namespace App\Models;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'internal_company_id',
        'name',
        'logo_img',
        'furigana_name',
        'business_industry_id',
        'office_address',
        'office_phone',
        'office_email1',
        'office_email2',
        'office_email3',
        'website_url',
        'client_liason',
        'admin_memo',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    // Company belongs to BusinessIndustry
    public function businessIndustry()
    {
        return $this->belongsTo('App\Models\BusinessIndustries', 'business_industry_id', 'id');
    }

    // Company has many Feedbacks
    public function feedbacks()
    {
        return $this->hasMany('App\Models\Feedbacks', 'company_id');
    }
}
