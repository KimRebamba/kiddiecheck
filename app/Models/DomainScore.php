<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainScore extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['test_id', 'domain_id', 'raw_score', 'scaled_score', 'scaled_score_based'];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
