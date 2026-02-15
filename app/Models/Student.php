<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
        'first_name', 'last_name', 'date_of_birth', 'family_id', 'feature_path', 'section_id'
=======
        'first_name', 'last_name', 'date_of_birth', 'family_id', 'feature_path', 'profile_image'
>>>>>>> Stashed changes
=======
        'first_name', 'last_name', 'date_of_birth', 'family_id', 'feature_path', 'profile_image'
>>>>>>> Stashed changes
=======
        'first_name', 'last_name', 'date_of_birth', 'family_id', 'feature_path', 'profile_image'
>>>>>>> Stashed changes
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the student's age in readable format (e.g., "5 years and 3 months")
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return 'N/A';
        }

        $birthDate = Carbon::parse($this->date_of_birth);
        $now = Carbon::now();
        $years = $birthDate->diffInYears($now);
        $months = $birthDate->copy()->addYears($years)->diffInMonths($now);
        
        if ($years > 0) {
            if ($months > 0) {
                return $years . ' year' . ($years > 1 ? 's' : '') . ' and ' . $months . ' month' . ($months > 1 ? 's' : '');
            }
            return $years . ' year' . ($years > 1 ? 's' : '');
        } else {
            return $months . ' month' . ($months > 1 ? 's' : '');
        }
    }

    /**
     * Get age in months for ECCD scoring calculations
     */
    public function getAgeInMonthsAttribute()
    {
        if (!$this->date_of_birth) {
            return 0;
        }

        $birthDate = Carbon::parse($this->date_of_birth);
        $now = Carbon::now();
        return $birthDate->diffInMonths($now);
    }

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

    protected static function boot()
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
                Log::warning('Failed to auto-generate assessment periods for student ' . $student->student_id . ': ' . $e->getMessage());
            }
        });
    }
}