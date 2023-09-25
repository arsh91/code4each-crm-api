<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentDependency extends Model
{
    use HasFactory;
    protected $table = 'component_dependencies_crm';

    protected $fillable = [
        'component_id',
        'name',
        'type',
        'path',
        'version'
    ];

}
