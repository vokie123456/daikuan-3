<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('telephone', 11)->unique()->comment('手机');
            $table->string('password')->comment('密码');
            $table->string('name', 45)->default('')->comment('姓名');
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别: 0 未知, 1 男性, 2 女性');
            $table->dateTime('birthday')->nullable()->comment('生日');
            $table->string('email')->default('')->comment('邮箱');
            $table->string('profession')->default('')->comment('职业');
            $table->string('address')->default('')->comment('地址');
            $table->string('share_code')->default('')->comment('分享码');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
