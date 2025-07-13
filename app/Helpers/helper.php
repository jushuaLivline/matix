<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!function_exists('is_weekend')) {
    /**
     * Check date is weekend
     *
     * @param $date
     * @return bool
     */
    function is_weekend($date): bool
    {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 || $weekDay == 6);
    }
}

// if (!function_exists('is_weekend')) {
//     /**
//      * Check date is weekend
//      *
//      * @param $date
//      * @return bool
//      */
//     function setting($settingCategory, $settingI): bool
//     {
//         return (new SettingService)
//                     ->settingCategory($settingCategory)
//                     ->settingId($id)
//                     ->get();
//     }
// }

if (!function_exists('getSearchLabel')) {
    function getSearchLabel($model)
    {
        $modelLabels = [
            'ProductNumber' => '品番',
            'ProductMaterial' => '材料',
            'ManufacturerInfo' => '材料メーカー',

            'Supplier' => '仕入先',
            'Customer' => '取引先',

            'Line' => 'ライン',
            'Department' => '部門',
            
            'NotSupplier' => 'ライン',
            'Project' => '計画',
            'Process' => 'プロセス',

        ];

        return $modelLabels[$model] ?? 'Default Label';
    }
}

if (!function_exists('getTableName')) {
    function getTableName($model){
        try{
            return (new $model)->getTable();
        } catch( Exception $exception){
            throw new Exception("No model: ". $model . ' found');
        }
    }
};

if (!function_exists('isIndexExistsInTable')) {
    function isIndexExistsInTable(string $table, string $index, $customized = false) {
        $prefix = Schema::getConnection()
            ->getTablePrefix();
        $table = implode([$prefix, $table]);
        $indexes = DB::select("show indexes from {$table}");
        $plucked = collect($indexes)->pluck('Key_name')->unique();
        $default = implode('_', [$table, $index, 'index']);
        return $plucked->contains(!$customized ? $default : $index);
    }
}
