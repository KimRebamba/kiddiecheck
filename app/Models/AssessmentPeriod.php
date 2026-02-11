<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AssessmentPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'index', 'starts_at', 'ends_at', 'teacher_grace_end', 'status'
    ];

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    public function isActive(): bool
    {
        $now = Carbon::now();
        return $now->between($this->starts_at, $this->ends_at);
    }

    public function isClosed(): bool
    {
        $now = Carbon::now();
        return $now->greaterThan($this->ends_at);
    }
}
