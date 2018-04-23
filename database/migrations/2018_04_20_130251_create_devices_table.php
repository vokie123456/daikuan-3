<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->unsigned()->comment('用户id');
            $table->string('unique_id')->unique()->comment('设备唯一性id');
            $table->string('name')->default('')->comment('包名');
            $table->string('operator', 32)->default('')->comment('运营商');
            $table->string('model', 32)->default('')->comment('设备型号');
            $table->string('phone_model', 32)->default('')->comment('手机型号');
            $table->string('phone_sys_version', 10)->default('')->comment('手机系统版本');
            $table->string('request_ip', 32)->default('')->comment('请求ip');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
