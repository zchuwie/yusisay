<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CensoredWord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Ensure 'user_id' is NOT listed here.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word',
    ];
}