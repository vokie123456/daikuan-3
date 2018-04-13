<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45)->comment('名称');
            $table->unsignedTinyInteger('type')->default(0)->comment('广告跳转类型: 0 app详情, 1 web页面');
            $table->unsignedTinyInteger('position')->default(0)->comment('显示位置: 0 首页, 1 贷款');
            $table->integer('app_id')->unsigned()->nullable()->comment('对应apps表的id');
            $table->string('url', 255)->default('')->comment('页面地址');
            $table->string('image', 255)->default('')->comment('广告图片');
            $table->timestamp('start_time')->nullable()->comment('起始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->integer('sort')->unsigned()->default(0)->comment('排序序号');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态: 0 禁止, 1 正常');
            $table->timestamps();
            $table->foreign('app_id')->references('id')->on('apps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
}
