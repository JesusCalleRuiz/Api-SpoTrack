<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'prospect',
        'status',
        'changed_at',
    ];
}
