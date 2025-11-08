<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CensoredWord extends Model
{
    use HasFactory;
    protected $table = 'censored_words';

    protected $fillable = ['word'];

    public $timestamps = true;
}