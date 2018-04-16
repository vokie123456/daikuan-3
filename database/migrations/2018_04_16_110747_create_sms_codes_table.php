<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 12)->comment('验证码');
            $table->string('telephone', 11)->comment('手机号码');
            $table->unsignedTinyInteger('type')->default(0)->comment('验证类型: 0 注册, 1 找回密码');
            $table->unsignedTinyInteger('isUse')->default(0)->comment('验证类型: 0 未使用, 1 已使用');
            $table->string('request_ip', 32)->default('')->comment('请求ip');
            $table->timestamp('expires_at')->nullable()->comment('过期时间');
            $table->timestamp('created_at')->useCurrent()->comment('添加时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_codes');
    }
}
