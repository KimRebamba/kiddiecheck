<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';
    protected $primaryKey = 'user_id';
    public $timestamps = true;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'home_address', 'phone_number', 'hire_date', 'feature_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_teacher', 'teacher_id', 'student_id')
            ->withPivot(['role', 'assigned_at', 'status']);
    }
}
