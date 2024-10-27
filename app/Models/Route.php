<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'path',
        'distance',
        'duration',
        'average_speed',
        'max_speed'
    ];
}
