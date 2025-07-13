<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;

trait CreateTableIndexes
{
    function createTableIndexes(Blueprint $table, array $indexes)
    {
        foreach ($indexes as $index) {
            if (isIndexExistsInTable($table->getTable(), $index)) continue;
            $table->index($index);
        }
    }
}
