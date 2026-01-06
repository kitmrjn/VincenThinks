<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;
    
    // Add 'parent_id' to the list
    protected $fillable = ['user_id', 'answer_id', 'content', 'parent_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function answer() {
        return $this->belongsTo(Answer::class);
    }

    // A reply can have a parent (if it's a sub-reply)
    public function parent() {
        return $this->belongsTo(Reply::class, 'parent_id');
    }

    // A reply can have many children (sub-replies)
    public function children() {
        return $this->hasMany(Reply::class, 'parent_id');
    }
}