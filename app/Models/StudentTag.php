<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTag extends Model
{
    protected $table = 'student_teacher';

    public $incrementing = false;

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
