<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'franchise_id',
        'branch_id',
        'student_id',
        'course_id',
        'certificate_number',
        'verification_code',
        'issue_date',
        'completion_date',
        'grade',
        'percentage',
        'template_name',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'completion_date' => 'date',
        'percentage' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($cert) {
            if (empty($cert->verification_code)) {
                $cert->verification_code = (string) Str::uuid();
            }
            if (empty($cert->certificate_number)) {
                $prefix = 'CERT-' . date('Y') . '-';
                $cert->certificate_number = $prefix . strtoupper(Str::random(8));
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function getVerificationUrlAttribute(): string
    {
        return url('/verify-certificate/' . $this->verification_code);
    }
}
