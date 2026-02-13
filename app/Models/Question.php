<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

        protected $primaryKey = 'question_id';
        protected $keyType = 'int';
        public $incrementing = true;

        protected $fillable = ['domain_id', 'text', 'question_type', 'order', 'display_text'];

        public function domain()
        {
            return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
        }

        public function responses()
        {
            return $this->hasMany(TestResponse::class, 'question_id', 'question_id');
        }

        public function pictures()
        {
            return $this->hasMany(TestPicture::class, 'question_id', 'question_id');        }
    }
