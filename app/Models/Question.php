<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'answer_latitude',
        'answer_longitude',
        'tolerance_meters',
        'difficulty',
        'description',
    ];

    protected $casts = [
        'answer_latitude' => 'float',
        'answer_longitude' => 'float',
        'tolerance_meters' => 'integer',
    ];
}
