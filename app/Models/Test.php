<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $primaryKey = 'test_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['period_id', 'student_id', 'examiner_id', 'test_date', 'notes', 'status', 'age_months'];

    protected $casts = [
        'test_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function assessmentPeriod()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function observer()
    {
        return $this->belongsTo(User::class, 'examiner_id', 'user_id');
    }

    public function responses()
    {
        return $this->hasMany(TestResponse::class, 'test_id', 'test_id');
    }

    public function domainScores()
    {
        return $this->hasMany(TestDomainScaledScore::class, 'test_id', 'test_id');
    }

    public function standardScore()
    {
        return $this->hasOne(\App\Models\TestStandardScore::class, 'test_id', 'test_id');
    }

    public function pictures()
    {
        return $this->hasMany(TestPicture::class, 'test_id', 'test_id');
    }

    public function scaledScores()
    {
        return $this->hasMany(TestDomainScaledScore::class, 'test_id', 'test_id');
    }

    public function isDomainsComplete(): bool
    {
        $domainCount = \App\Models\Domain::count();
        if ($domainCount <= 0) { return false; }
        $completed = 0;
        foreach ($this->scaledScores as $s) {
            if ($s->scaled_score !== null) { $completed++; }
        }
        return $completed >= $domainCount;
    }

    public function isAllNA(): bool
    {
        $domains = $this->scaledScores;
        if ($domains->isEmpty()) { return false; }
        foreach ($domains as $s) {
            $based = $s->scaled_score_based;
            if ($based !== null && (float)$based > 0) {
                return false;
            }
        }
        return true;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Test $test) {
            if ($test->test_date && $test->student_id) {
                try {
                    $student = $test->student;
                    if (!$student) {
                        $student = \App\Models\Student::find($test->student_id);
                    }
                    if ($student && $student->dob) {
                        $testDate = \Illuminate\Support\Carbon::parse($test->test_date);
                        $dob = \Illuminate\Support\Carbon::parse($student->dob);
                        $days = $dob->diffInDays($testDate);
                        $months = $days / 30;
                        $test->age_months = round($months, 2);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Failed to compute age months for test ' . $test->test_id . ': ' . $e->getMessage());
                }
            }
        });
    }
}
