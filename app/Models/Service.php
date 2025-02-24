<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'short_desc',
        'content',
        'image',
        'status',
    ];
    // create a schope object active inavctive mode
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
