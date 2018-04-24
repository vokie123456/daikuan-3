<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotes', function (Blueprint $table) {
            $table->integer('app_id')->unsigned()->comment('APPid');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->timestamp('created_at')->useCurrent()->comment('添加时间');
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
        Schema::dropIfExists('promotes');
    }
}
