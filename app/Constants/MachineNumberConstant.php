<?php

namespace App\Constants;

class MachineNumberConstant {

    // 0:自社製設備機械・工具
    // 1:購入機械
    // 2:その他
    // 3:試作・ライン治具
    // 4:外販機械
    public const MACHINE_DIVISION = [
        0 => '自社製設備機械・工具',
        1 => '購入機械',
        2 => 'その他',
        3 => '試作・ライン治具',
        4 => '外販機械'
    ];

    public function getConstants(): array
    {
        return [
            'MACHINE_DIVISION' => self::MACHINE_DIVISION
        ];
    }
}