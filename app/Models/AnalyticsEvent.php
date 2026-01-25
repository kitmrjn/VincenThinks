<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'message', 'meta_data'];

    protected $casts = [
        'meta_data' => 'array',
    ];
}