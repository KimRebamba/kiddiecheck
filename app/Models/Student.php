<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id', 'section_id', 'name', 'dob', 'emergency_contact', 'gender',
        'handedness', 'is_studying', 'school_name',
        'enrollment_date', 'status', 'profile_path', 'notes'
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_student', 'student_id', 'teacher_id')
            ->withPivot(['role', 'assigned_at', 'status']);
    }

    public function tags()
    {
        return $this->hasMany(StudentTag::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    public function assessmentPeriods()
    {
        return $this->hasMany(AssessmentPeriod::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (Student $student) {
            try {
                // Auto-generate three assessment periods spaced six months apart
                $months = (int) config('eccd.period.months', 6);
                $graceDays = (int) config('eccd.period.teacher_grace_days', 7);
                $start = \Illuminate\Support\Carbon::parse($student->enrollment_date)->startOfDay();

                for ($i = 1; $i <= 3; $i++) {
                    $pStart = (clone $start)->addMonths($months * ($i - 1));
                    $pEnd = (clone $pStart)->addMonths($months)->subSecond();
                    $grace = (clone $pEnd)->addDays($graceDays);

                    $student->assessmentPeriods()->create([
                        'index' => $i,
                        'starts_at' => $pStart,
                        'ends_at' => $pEnd,
                        'teacher_grace_end' => $grace,
                        'status' => $i === 1 ? 'active' : 'scheduled',
                    ]);
                }
            } catch (\Throwable $e) {
                // Silently ignore generation errors to avoid blocking creation
            }
        });
    }
}
