<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryUpload extends Model
{
    use HasFactory;

    protected $guarded = [];

    function extension(){
        return explode(".", $this->file_name)[1];
    }
}
