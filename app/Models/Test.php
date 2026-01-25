<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'observer_id', 'test_date', 'status', 'started_at', 'submitted_by', 'submitted_at'];

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function observer()
    {
        return $this->belongsTo(User::class, 'observer_id');
    }

    public function responses()
    {
        return $this->hasMany(TestResponse::class);
    }

    public function scores()
    {
        return $this->hasMany(DomainScore::class);
    }

    public function pictures()
    {
        return $this->hasMany(TestPicture::class);
    }
}
