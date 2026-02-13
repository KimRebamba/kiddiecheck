<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResponse extends Model
{
    protected $table = 'test_responses';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $guarded = [];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }
}
