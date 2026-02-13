<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $primaryKey = 'question_id';

    protected $guarded = [];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }

    public function scaleVersion()
    {
        return $this->belongsTo(ScaleVersion::class, 'scale_version_id', 'scale_version_id');
    }

    public function responses()
    {
        return $this->hasMany(TestResponse::class, 'question_id', 'question_id');
    }
}
