<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCubeTable extends Migration
{
    public $tableName = "cube";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable($this->tableName)) $this->create();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }

    /**
     * 执行创建表
     */
    private function create()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';      // 设置存储引擎
            $table->charset = 'utf8';       // 设置字符集
            $table->collation  = 'utf8_general_ci';       // 设置排序规则

            $table->id();
            $table->string('name', 100)->nullable(false)->comment("魔方名称")->unique("name_unique");
            $table->tinyInteger("style")->nullable(false)->default(0)->comment("模块样式 0=默认样式 1=样式一 2=样式二");
            $table->longText("data")->nullable(false)->comment("魔方图片列表");
            $table->timestamps();
        });
        $prefix = DB::getConfig('prefix');
        $qu = "ALTER TABLE " . $prefix . $this->tableName . " comment '图片魔方表'";
        DB::statement($qu);
    }
}
