<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'assessment_period_id', 'observer_id', 'test_date', 'status', 'started_at', 'submitted_by', 'submitted_at'];

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assessmentPeriod()
    {
        return $this->belongsTo(AssessmentPeriod::class);
    }

    public function observer()
    {
        return $this->belongsTo(User::class, 'observer_id');
    }

    public function responses()
    {
        return $this->hasMany(TestResponse::class);
    }

    public function scores()
    {
        return $this->hasMany(DomainScore::class);
    }

    public function pictures()
    {
        return $this->hasMany(TestPicture::class);
    }

    // Scopes/helpers
    public function scopeFinalized($query)
    {
        return $query->whereIn('status', ['finalized', 'completed']);
    }

    public function scopeVisibleForRole($query, string $role)
    {
        if ($role === 'admin') {
            return $query->whereNotIn('status', ['archived']);
        }
        // Teachers & families only see finalized/completed tests globally
        return $query->whereIn('status', ['finalized', 'completed']);
    }

    public function isDomainsComplete(): bool
    {
        $domainCount = \App\Models\Domain::count();
        if ($domainCount <= 0) { return false; }
        $completed = 0;
        foreach ($this->scores as $s) {
            if ($s->scaled_score !== null) { $completed++; }
        }
        return $completed >= $domainCount;
    }

    public function isAllNA(): bool
    {
        $domains = $this->scores;
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
                    $student = $test->student ?? Student::find($test->student_id);
                    if ($student && $student->dob) {
                        $testDate = \Illuminate\Support\Carbon::parse($test->test_date);
                        $dob = \Illuminate\Support\Carbon::parse($student->dob);
                        $days = $dob->diffInDays($testDate);
                        $months = $days / 30;
                        $test->age_months = round($months, 2);
                    }
                } catch (\Throwable $e) {
                    // silently ignore compute errors
                }
            }

            if ($test->examiner_id && empty($test->examiner_name)) {
                $examiner = User::find($test->examiner_id);
                $test->examiner_name = $examiner?->name;
            }
        });
    }
}
