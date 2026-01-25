<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTag extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['student_id', 'tag_type', 'notes'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
