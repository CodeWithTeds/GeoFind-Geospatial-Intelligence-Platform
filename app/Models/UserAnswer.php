<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'question_id',
        'answer_latitude',
        'answer_longitude',
        'stars',
        'is_correct',
        'answered_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'answer_latitude' => 'decimal:6',
        'answer_longitude' => 'decimal:6',
        'stars' => 'integer',
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    /**
     * Disable timestamps if not needed, or rely on answered_at.
     * The user schema only mentions answered_at (created_at equivalent).
     * Typically Laravel models have created_at and updated_at.
     * The user schema does NOT have created_at/updated_at.
     * It has answered_at.
     * I will disable default timestamps to match the schema strictly.
     */
    public $timestamps = false;

    /**
     * Get the user that owns the answer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the question that was answered.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
