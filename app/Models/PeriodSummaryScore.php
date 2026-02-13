<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodSummaryScore extends Model
{
    use HasFactory;

    protected $table = 'period_summary_scores';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'period_id',
        'teachers_standard_score_avg',
        'family_standard_score',
        'final_standard_score',
        'final_interpretation',
        'teacher_discrepancy',
        'teacher_family_discrepancy'
    ];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }
}
