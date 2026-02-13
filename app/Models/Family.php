<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

        protected $primaryKey = 'user_id';
        protected $keyType = 'int';
        public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'family_id', 'user_id');
        return $this->hasMany(Student::class, 'family_id', 'user_id');
    }
}
