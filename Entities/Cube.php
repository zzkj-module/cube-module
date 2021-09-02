<?php
/**
 * Created By PhpStorm.
 * User: Li Ming
 * Date: 2021-08-03
 * Fun: 商品表
 */

namespace Modules\Cube\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Cube extends BaseModel
{
    use HasFactory;
    protected $table = "cube";

    /**
     * 获取样式列表
     * @return string[]
     */
    public static function getStyleArr()
    {
        return [
            0 => "默认样式",
            1 => "样式一",
            2 => "样式二",
        ];
    }
}