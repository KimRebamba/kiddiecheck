<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $primaryKey = 'student_id';

    protected $guarded = [];

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id', 'user_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'student_teacher', 'student_id', 'teacher_id');
    }

    public function assessmentPeriods()
    {
        return $this->hasMany(AssessmentPeriod::class, 'student_id', 'student_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'student_id', 'student_id');
    }
}
