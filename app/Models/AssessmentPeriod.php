<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AssessmentPeriod extends Model
{
    use HasFactory;

    protected $table = 'assessment_periods';
    protected $primaryKey = 'period_id';
    public $timestamps = true;

    protected $fillable = [
        'student_id', 'description', 'start_date', 'end_date', 'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'period_id', 'period_id');
    }

    public function isActive(): bool
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    public function isClosed(): bool
    {
        $now = Carbon::now();
        return $now->greaterThan($this->end_date);
    }
}
