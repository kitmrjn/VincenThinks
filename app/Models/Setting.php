<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // --- THIS IS THE MISSING PART CAUSING THE ERROR ---
    protected $fillable = ['key', 'value'];
}