<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('version')->unsigned()->comment('版本号');
            $table->unsignedTinyInteger('type')->comment('类型: 0 android, 1 ios');
            $table->string('url', 255)->comment('下载地址');
            $table->text('details')->nullable()->comment('详细介绍');
            $table->unsignedTinyInteger('isForce')->default(1)->comment('是否强制更新: 0 非强制, 1 强制');
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
        Schema::dropIfExists('versions');
    }
}
