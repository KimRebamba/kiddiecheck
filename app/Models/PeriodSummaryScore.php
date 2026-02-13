<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodSummaryScore extends Model
{
    use HasFactory;

    protected $table = 'period_summary_scores';

    protected $guarded = [];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }
}
