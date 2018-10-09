<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('type', 45)->comment('设置组');
            $table->string('code', 45)->comment('组内的键名');
            $table->text('value')->nullable()->comment('Code对应的值');
            $table->unsignedTinyInteger('serialize')->default(0)->comment('是否序列化: 0不序列化, 1序列化');
            $table->primary(['type', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
