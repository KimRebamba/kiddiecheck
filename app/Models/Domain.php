<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

        protected $primaryKey = 'domain_id';
        protected $keyType = 'int';
        public $incrementing = true;

        protected $fillable = ['name', 'description'];

        public function questions()
        {
            return $this->hasMany(Question::class, 'domain_id', 'domain_id');
        }

        public function scores()
        {
            return $this->hasMany(TestDomainScaledScore::class, 'domain_id', 'domain_id');        }
    }