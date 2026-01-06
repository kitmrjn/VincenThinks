<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ['admin_id', 'action', 'target_user_name', 'details', 'ip_address'];

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}