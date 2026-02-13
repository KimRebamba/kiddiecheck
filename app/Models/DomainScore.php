<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainScore extends Model
{
    use HasFactory;

    protected $table = 'test_domain_scaled_scores';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = ['test_id', 'domain_id', 'scale_version_id', 'raw_score', 'scaled_score'];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }
}
