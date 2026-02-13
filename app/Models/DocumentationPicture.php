<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationPicture extends Model
{
    use HasFactory;

    protected $table = 'documentation_pictures';
    protected $primaryKey = 'picture_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = ['file_path'];

    public function tests()
    {
        return $this->hasMany(TestPicture::class, 'picture_id', 'picture_id');
    }
}
