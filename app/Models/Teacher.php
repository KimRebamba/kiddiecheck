<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'hire_date', 'status'
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'teacher_student', 'teacher_id', 'student_id')
            ->withPivot(['role', 'assigned_at', 'status']);
    }
}
