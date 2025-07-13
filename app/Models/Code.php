<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    protected $table = 'codes';

    public static function getDefectReasons()
    {
        return self::selectRaw('division, code, name')
            ->where('division', '材不理由')
            ->get();
    }
}
