<?php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService{

    private $cachePrefix = "setting-";
    private $settingCategory, $settingId;

    function settingCategory($settingCategory){
        $this->settingCategory = $settingCategory;
        return $this;
    }

    function settingId($settingId){
        $this->settingId = $settingId;
        return $this;
    }

    function get(){
        $key = $this->generateKey();
        return Cache::rememberForever($key, function() use ($key) {
            return optional(Setting::where("setting_category", $this->settingCategory)
                        ->where("setting_id", $this->settingId)
                        ->first());
        });
    }


    function updateSetting(array $value){
        $key = $this->generateKey();
        Cache::forget($key);
        return Cache::rememberForever($key, function() use ($value) {
            Setting::updateOrCreate([
                'setting_category' => $this->settingCategory,
                'setting_id' => $this->settingId,
            ], $value);
           return $this->get();
        });
    }


    function delete(){
        Cache::forget($this->generateKey());
        return Setting::where("setting_category", $this->settingCategory)
                ->where("setting_id", $this->settingId)    
                ->delete();
    }

    function generateKey(){
        return $this->cachePrefix . '-' . $this->settingCategory . "-" .  $this->settingId;
    }

}