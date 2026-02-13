<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationPicture extends Model
{
    use HasFactory;

    protected $table = 'documentation_pictures';

    protected $primaryKey = 'picture_id';

    protected $guarded = [];

    public function tests()
    {
        return $this->belongsToMany(Test::class, 'test_picture', 'picture_id', 'test_id');
    }
}
