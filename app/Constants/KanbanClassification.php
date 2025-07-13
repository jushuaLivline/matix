<?php

namespace App\Constants;

class KanbanClassification {

    // 1:支給材 2:外注加工 3:外注支給 4:社内
    public const KANBAN_CLASSIFICATION = [
        1 => '支給材',
        2 => '外注加工',
        3 => '外注支給',
        4 => '社内'
    ];

    public function getConstants(): array
    {
        return [
            'KANBAN_CLASSIFICATION' => self::KANBAN_CLASSIFICATION
        ];
    }
}
