<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'observer_id', 'test_date', 'status', 'started_at', 'submitted_by', 'submitted_at'];

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class);
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
