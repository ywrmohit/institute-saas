<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamQuestion extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'franchise_id',
        'exam_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'marks',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
        'marks' => 'integer',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
