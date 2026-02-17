<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $primaryKey = 'section_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = ['name', 'description'];

    public function students()
    {
        return $this->hasMany(Student::class, 'section_id', 'section_id');
    }
}
