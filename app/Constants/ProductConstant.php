<?php

namespace App\Constants;

class ProductConstant {

    public const CATEGORY = [
        1 => '材料',
        2 => '製品',
        3 => '試作品',
        4 => '購入材',
        5 => '仕掛品'
    ];

    // 1:かんばん 2:指示
    public const INSTRUCTION_CLASS = [
        0 => 'かんばん',
        1 => '指示'
    ];

    // 0:号試 1:号口 2:補給 3:廃止
    public const PRODUCTION_DIVISION = [
        1 => '号試',
        2 => '号口',
        3 => '補給',
        4 => '廃止'
    ];

    public function getConstants(): array
    {
        return [
            'CATEGORY' => self::CATEGORY,
            'INSTRUCTION_CLASS' => self::INSTRUCTION_CLASS,
            'PRODUCTION_DIVISION' => self::PRODUCTION_DIVISION

        ];
    }
}