<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
    protected $fillable = ['answer_id', 'score', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
