<?php
/**
 * Created By PhpStorm.
 * User: Li Ming
 * Date: 2021-08-25
 * Fun:
 */

namespace Modules\Cube\Entities;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    /**
     * 获取域名
     */
    public static function getDomain()
    {
        $ht = env('APP_URL') ?? "";
        if($ht == ""){
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $ht = $http_type . $_SERVER["HTTP_HOST"];
        }
        return $ht;
    }

    /**
     * 设置图片添加域名
     * @return string
     */
    public static function setPicUrl(string $pic)
    {
        if($pic == "") return "";
        $ht = self::getDomain();
        return $ht . "/" . $pic;
    }
}