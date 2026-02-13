<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'first_name', 'last_name', 'date_of_birth', 'family_id', 'feature_path'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id', 'user_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'student_teacher', 'student_id', 'teacher_id', 'student_id', 'user_id')
            ->withPivot(['role', 'assigned_at', 'status']);
    }

    public function tags()
    {
        return $this->hasMany(StudentTag::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'student_id', 'student_id');
    }

    public function assessmentPeriods()
    {
        return $this->hasMany(AssessmentPeriod::class, 'student_id', 'student_id');
    }

    public function tests()
    {
        parent::boot();

        static::created(function (Student $student) {
            try {
                // Auto-generate three assessment periods spaced six months apart
                $start = \Illuminate\Support\Carbon::parse($student->created_at)->startOfDay();

                for ($i = 1; $i <= 3; $i++) {
                    $pStart = (clone $start)->addMonths(6 * ($i - 1));
                    $pEnd = (clone $pStart)->addMonths(6);

                    $student->assessmentPeriods()->create([
                        'description' => "Assessment Period $i",
                        'start_date' => $pStart->toDateString(),
                        'end_date' => $pEnd->toDateString(),
                        'status' => 'scheduled',
                    ]);
                }
            } catch (\Throwable $e) {
                // Silently ignore generation errors to avoid blocking creation
            }
        });
    }
}
