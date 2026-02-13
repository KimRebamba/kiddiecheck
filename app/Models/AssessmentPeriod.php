<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentPeriod extends Model
{
    use HasFactory;

    protected $primaryKey = 'period_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'description', 'start_date', 'end_date', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function tests()
    {
        return $this->hasMany(Test::class, 'period_id', 'period_id');
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
