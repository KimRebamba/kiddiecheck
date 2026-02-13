<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScaleVersion extends Model
{
    use HasFactory;

    protected $table = 'scale_versions';

    protected $primaryKey = 'scale_version_id';

    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany(Question::class, 'scale_version_id', 'scale_version_id');
    }

    public function domainScaledScores()
    {
        return $this->hasMany(TestDomainScaledScore::class, 'scale_version_id', 'scale_version_id');
    }

    public function standardScoreScales()
    {
        return $this->hasMany(StandardScoreScale::class, 'scale_version_id', 'scale_version_id');
    }

    public function testStandardScores()
    {
        return $this->hasMany(TestStandardScore::class, 'scale_version_id', 'scale_version_id');
    }
}
