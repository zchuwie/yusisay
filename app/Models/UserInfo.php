<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_picture',
    ];

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function getProfilePicture()
    {
        if ($this->userInfo && $this->userInfo->profile_picture) {
            return asset('assets/' . $this->userInfo->profile_picture);
        }
        return null;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
