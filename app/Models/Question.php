<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['domain_id', 'question_text', 'type', 'instructions'];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function responses()
    {
        return $this->hasMany(TestResponse::class);
    }

    public function pictures()
    {
        return $this->hasMany(TestPicture::class);
    }
}
