<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $table = 'domains';

    protected $primaryKey = 'domain_id';

    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany(Question::class, 'domain_id', 'domain_id');
    }

    public function scaledScores()
    {
        return $this->hasMany(DomainScore::class, 'domain_id', 'domain_id');
    }

    public function testDomainScores()
    {
        return $this->hasMany(TestDomainScaledScore::class, 'domain_id', 'domain_id');
    }
}
