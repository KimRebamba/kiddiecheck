<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestPicture extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['test_id', 'question_id', 'file_path'];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
