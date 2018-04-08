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
            $table->integer('company_id')->unsigned()->comment('归属公司');
            $table->string('synopsis', 120)->default('')->comment('简介');
            $table->text('details')->nullable()->comment('详细介绍');
            $table->unsignedDecimal('rate', 5, 2)->comment('利率');
            $table->unsignedTinyInteger('rate_type')->default(0)->comment('利率类型: 0 日, 1 周, 2 月, 3 年');
            $table->string('moneys', 1000)->default('')->comment('借款金额(json)');
            $table->string('terms', 1000)->default('')->comment('借款期限(json)');
            $table->string('repayments', 1000)->default('')->comment('还款方式(json)');
            $table->integer('apply_number')->default(0)->comment('申请人数');
            $table->unsignedTinyInteger('recommend')->default(0)->comment('推荐指数: 0-10(1个为半星)');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态: 0 禁止, 1 正常');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies');
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
