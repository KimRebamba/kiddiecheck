<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestDomainScaledScore extends Model
{
    protected $table = 'test_domain_scaled_scores';

    public $incrementing = false;

    protected $primaryKey = null;

    protected $guarded = [];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }

    public function scaleVersion()
    {
        return $this->belongsTo(ScaleVersion::class, 'scale_version_id', 'scale_version_id');
    }
}
