<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResponse extends Model
{
    protected $table = 'test_responses';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['test_id', 'question_id', 'is_assumed', 'response'];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }
}
