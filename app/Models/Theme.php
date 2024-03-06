<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function dependencies()
    {
        return $this->hasMany(ThemeDependency::class, 'theme_id');
    }
}
