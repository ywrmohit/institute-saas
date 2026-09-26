<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'franchise_id',
        'name',
        'code',
        'category',
        'type',
        'duration_weeks',
        'total_fee',
        'registration_fee',
        'description',
        'eligibility',
        'certificate_title',
        'status',
    ];

    protected $casts = [
        'total_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'duration_weeks' => 'integer',
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function studyMaterials(): HasMany
    {
        return $this->hasMany(StudyMaterial::class);
    }
}
