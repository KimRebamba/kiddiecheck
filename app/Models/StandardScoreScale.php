<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardScoreScale extends Model
{
    use HasFactory;

    protected $table = 'standard_score_scales';

    protected $guarded = [];

    public function scaleVersion()
    {
        return $this->belongsTo(ScaleVersion::class, 'scale_version_id', 'scale_version_id');
    }
}
