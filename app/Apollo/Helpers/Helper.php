<?php

/**
 * @author lvt20160109
 */

namespace App\Apollo\Helpers;
use Illuminate\Support\Str;

class Helper
{
    protected static function removeAccent($string)
    {
        $marTViet = [
            // lowercase letters
            "à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ","ặ","ẳ","ẵ",
            "è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ",
            "ì","í","ị","ỉ","ĩ",
            "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ","ờ","ớ","ợ","ở","ỡ",
            "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
            "ỳ","ý","ỵ","ỷ","ỹ",
            "đ","Đ","'",
            // uppercase
            "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă","Ằ","Ắ","Ặ","Ẳ","Ẵ",
            "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
            "Ì","Í","Ị","Ỉ","Ĩ",
            "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ","Ờ","Ớ","Ợ","Ở","Ỡ",
            "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
            "Ỳ","Ý","Ỵ","Ỷ","Ỹ",
            "Đ","Đ","'",
        ];
        $marKoDau = [
            /// lowercase letters
            "a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","a",
            "e","e","e","e","e","e","e","e","e","e","e",
            "i","i","i","i","i",
            "o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o",
            "u","u","u","u","u","u","u","u","u","u","u",
            "y","y","y","y","y",
            "d","D","",
            // uppercase
            "A","A","A","A","A","A","A","A","A","A","A","A","A","A","A","A","A",
            "E","E","E","E","E","E","E","E","E","E","E",
            "I","I","I","I","I",
            "O","O","O","O","O","O","O","O","O","O","O","O","O","O","O","O","O",
            "U","U","U","U","U","U","U","U","U","U","U",
            "Y","Y","Y","Y","Y",
            "D","D","",
        ];
        return str_replace($marTViet, $marKoDau, $string);
    }

    public static function getSlug($string)
    {
        return Str::slug(static::removeAccent($string));
    }

    public static function shout(string $string)
    {
        return strtoupper($string);
    }

    public static function createFolder($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    public static function setActive($path, $active = 'active')
    {
        return call_user_func_array('Request::is', (array)$path) ? $active : '';
    }

    public static function showPhone($phone, $delimiter = ' ')
    {
        $result = $phone;
        if (preg_match('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', $phone, $matches)) {
            $result = $matches[1] . $delimiter .$matches[2] . $delimiter . $matches[3];
            return $result;
        }
        return $phone;
    }

    public static function domainNameArray()
    {
        $arrCate = [];
        $arr = self::originDomainName();
        foreach($arr as $value) {
            $arrCate[$value['id']] = $value['name'];
        }
        return $arrCate;
    }

    public static function hostsArray()
    {
        // $arrCate = [
        //     'localhost' => 1,
        // ];
        $arrCate = [];
        $arr = self::originDomainName();
        foreach($arr as $value) {
            if ($value['id'] != 0) {
                $arrCate[$value['name']] = $value['id'];
            }
        }
        return $arrCate;
    }

    public static function domainNameObject()
    {
        $arrCate = self::originDomainName();
        $arrCate = json_encode($arrCate);
        $arrCate = json_decode($arrCate);
        return $arrCate;
    }

    public static function getHostDefault()
    {
        if (request()->hasSession()) {
            $domainID = request()->session()->get('domain_id');
            if ($domainID) {
                return $domainID;
            }
        }
        $host = request()->getHttpHost();
        $host = strtolower($host);
        $arrCate = self::originDomainName();
        foreach ( $arrCate as $value) {
            if ($value['name'] == $host) {
                return $value['id'];
            }
        }
        return 0;
    }

    public static function getHostID()
    {
        $host = request()->getHttpHost();
        $host = strtolower($host);
        $arrCate = self::originDomainName();
        foreach ( $arrCate as $value) {
            if ($value['name'] == $host) {
                return $value['id'];
            }
        }
        return 0;
    }

    public static function originDomainName()
    {
        $domain_allow = config('app.domain_allow');

        $arrCate = [
            ['id' => 0, 'name' => 'Choose domain name'],
        ];
        if (is_array($domain_allow) AND count($domain_allow)) {
            foreach ($domain_allow as $key => $value) {
                $arrCate[] = ['id' => $key + 1, 'name' => $value];
            }
        }
        return $arrCate;
    }

    public static function dirToArray($dir)
    {
        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                }else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }

    public static function truncate($string, $length, $dots = '...') {
        return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
    }
}
