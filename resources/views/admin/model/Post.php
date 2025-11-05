<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Admin\Models\Report;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'post_id');
    }
}