<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestStandardScore extends Model
{
    use HasFactory;

    protected $table = 'test_standard_scores';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = ['test_id', 'scale_version_id', 'sum_scaled_scores', 'standard_score', 'interpretation'];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }
}
