<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $table = 'tests';

    protected $primaryKey = 'test_id';

    protected $guarded = [];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function examiner()
    {
        return $this->belongsTo(User::class, 'examiner_id', 'user_id');
    }

    public function responses()
    {
        return $this->hasMany(TestResponse::class, 'test_id', 'test_id');
    }

    public function pictures()
    {
        return $this->belongsToMany(DocumentationPicture::class, 'test_picture', 'test_id', 'picture_id');
    }

    public function domainScores()
    {
        return $this->hasMany(TestDomainScaledScore::class, 'test_id', 'test_id');
    }

    public function standardScore()
    {
        return $this->hasOne(TestStandardScore::class, 'test_id', 'test_id');
    }
}
