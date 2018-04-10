<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_apps', function (Blueprint $table) {
            $table->integer('app_id')->unsigned()->comment('APP表的id');
            $table->integer('category_id')->unsigned()->comment('类别表的id');
            $table->foreign('app_id')->references('id')->on('apps');
            $table->foreign('category_id')->references('id')->on('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_apps');
    }
}
