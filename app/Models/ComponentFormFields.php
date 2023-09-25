<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentFormFields extends Model
{
    use HasFactory;
    protected $fillable = [
        'component_id',
        'field_name',
        'field_type',
        'default_value',
    ];

}
