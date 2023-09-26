<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteCategory extends Model
{
    use HasFactory;

    public function agencyWebsites()
    {
        return $this->hasMany(AgencyWebsite::class, 'website_category_id');
    }
}
