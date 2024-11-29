<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentPlan extends Model
{
    use HasFactory;
    protected $table = 'current_plan';

    protected $fillable = [
        'agency_id',
        'website_id',
        'plan_id',
        'website_start_date',
        'status',
        'planexpired',
    ];      
}
