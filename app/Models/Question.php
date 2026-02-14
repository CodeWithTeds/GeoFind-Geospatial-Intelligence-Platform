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
        'level',
        'description',
    ];

    protected $casts = [
        'answer_latitude' => 'float',
        'answer_longitude' => 'float',
        'tolerance_meters' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'answer_latitude',
        'answer_longitude',
    ];
}
