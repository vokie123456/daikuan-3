<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('app_id')->unsigned()->comment('APPid');
            $table->integer('user_id')->nullable()->unsigned()->comment('用户id');
            $table->string('ip', 32)->default('')->comment('请求ip');
            $table->unsignedTinyInteger('click_promote')->default(0)->comment('是否推广: 0 未点击推广, 1 已点击推广');
            $table->timestamps();
            $table->foreign('app_id')->references('id')->on('apps');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_records');
    }
}
