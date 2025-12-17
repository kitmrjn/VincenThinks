<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Department extends Model
{
    // These are the only fields allowed to be saved to the database
    protected $fillable = [
        'name',
        'acronym',
    ];

    /**
     * Relationship: A department has many teachers (users)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}