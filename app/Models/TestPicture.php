<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPicture extends Model
{
    protected $table = 'test_picture';

    public $incrementing = false;

    protected $table = 'test_picture';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['test_id', 'picture_id'];

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }

    public function picture()
    {
        return $this->belongsTo(\App\Models\DocumentationPicture::class, 'picture_id', 'picture_id');
    }
}
