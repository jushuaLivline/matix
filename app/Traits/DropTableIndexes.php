<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait DropTableIndexes
{
    function dropTableIndexes(Blueprint $blueprint, array $indexes, $customized = false)
    {
        foreach ($indexes as $index) {
            $prefix = Schema::getConnection()
                ->getTablePrefix();
            $table = $blueprint->getTable();
            if (!isIndexExistsInTable($table, $index, $customized)) continue;
            $table = implode([$prefix, $table]);
            $default = implode('_', [$table, $index, 'index']);
            $blueprint->dropIndex(!$customized ? $default : $index);
        }
    }
}
