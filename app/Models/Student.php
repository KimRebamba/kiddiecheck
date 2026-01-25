<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id', 'section_id', 'name', 'dob', 'emergency_contact', 'gender', 'enrollment_date', 'status', 'profile_path', 'notes'
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
}
