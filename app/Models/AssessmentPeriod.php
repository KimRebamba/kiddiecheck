<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentPeriod extends Model
{
    use HasFactory;

    protected $table = 'assessment_periods';

    protected $primaryKey = 'period_id';

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'period_id', 'period_id');
    }

    public function summary()
    {
        return $this->hasOne(PeriodSummaryScore::class, 'period_id', 'period_id');
    }
}
