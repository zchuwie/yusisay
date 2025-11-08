<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'is_anonymous',
        'content',
        'is_hidden',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getIsHiddenAttribute()
    {
        return $this->attributes['is_hidden'];
    }

    public function setIsHiddenAttribute($value)
    {
        $this->attributes['is_hidden'] = $value;
    }
}
