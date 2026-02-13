<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPicture extends Model
{
    protected $table = 'test_picture';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $guarded = [];
}
