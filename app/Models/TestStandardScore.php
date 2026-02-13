<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestStandardScore extends Model
{
    use HasFactory;

    protected $table = 'test_standard_scores';

    protected $guarded = [];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }

    public function scaleVersion()
    {
        return $this->belongsTo(ScaleVersion::class, 'scale_version_id', 'scale_version_id');
    }
}
