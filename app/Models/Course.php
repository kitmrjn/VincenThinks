<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name', 'acronym', 'type'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}