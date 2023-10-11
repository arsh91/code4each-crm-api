<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyWebsite extends Model
{
    use HasFactory;
    protected $fillable = [
        'website_category_id',
        'business_name',
        'address',
        'description',
        'logo',
        'agency_id',
        'status',
        'website_id',
        'created_by',
    ];

    public function websiteCategory()
    {
        return $this->belongsTo(WebsiteCategory::class, 'website_category_id');
    }

    public function websiteDetail()
    {
        return $this->belongsTo(Websites::class, 'website_id');
    }
}
