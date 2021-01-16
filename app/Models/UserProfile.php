<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $guarded = ['user_id', 'membership_status'];
    protected $hidden = ['id', 'user_id', 'created_at', 'updated_at'];

    protected $casts = [
        'facility' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

