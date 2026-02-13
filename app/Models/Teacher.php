<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

        protected $primaryKey = 'user_id';
        protected $keyType = 'int';
        public $incrementing = false;
        public $timestamps = false;

        protected $fillable = [
            'user_id', 'hire_date', 'status'
        ];

        public function user()
        {
            return $this->belongsTo(User::class, 'user_id');
        }

        public function students()
        {
            return $this->belongsToMany(Student::class, 'student_teacher', 'teacher_id', 'student_id', 'user_id', 'student_id')
                ->withPivot(['role', 'assigned_at', 'status']);
        }
    }
