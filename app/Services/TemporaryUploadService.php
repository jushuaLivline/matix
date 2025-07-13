<?php

namespace App\Services;

use App\Models\TemporaryUpload;

class TemporaryUploadService
{

    function getTemporaryFiles(array $data){
        return TemporaryUpload::where([
            'form' => $data['form'],
            'user_id' => $data['user_id'],
        ])->get();
    }

    function deleteTemporaryFiles(array $data){
        return TemporaryUpload::where([
            'form' => $data['form'],
            'user_id' => $data['user_id'],
        ])->delete();
    }
}