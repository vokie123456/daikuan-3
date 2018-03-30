<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45)->comment('名称');
            $table->string('weburl', 255)->default('')->comment('推广地址');
            $table->string('icon', 255)->default('')->comment('图标');
            $table->string('company', 45)->default('')->comment('归属公司');
            $table->string('synopsis', 120)->default('')->comment('归属公司');
            $table->text('details')->nullable()->comment('详细介绍');
            $table->unsignedDecimal('rate', 5, 2)->comment('利率');
            $table->unsignedTinyInteger('rate_type')->default(0)->comment('利率类型: 0 日, 1 周, 2 月, 3 年');
            $table->string('moneys', 1000)->default('')->comment('借款金额(serialize)');
            $table->string('terms', 1000)->default('')->comment('借款期限(serialize)');
            $table->string('repayments', 1000)->default('')->comment('还款方式(serialize)');
            $table->integer('apply_number')->default(0)->comment('申请人数(造假): 大于等于0则直接显示, 小于0则随机生成apply_rand范围内的一个数值');
            $table->string('apply_rand', 120)->default('')->comment('随机生成申请人数的范围(serialize), 当apply_number小于0时有效');
            $table->unsignedTinyInteger('recommend')->default(0)->comment('推荐指数: 0-10(1个为半星)');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态: 0 禁止, 1 正常');
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
        Schema::dropIfExists('apps');
    }
}
