<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCategoryAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_apps', function (Blueprint $table) {
            //
            $table->integer('sort')->unsigned()->default(0)->comment('排序序号(倒序排列)')->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_apps', function (Blueprint $table) {
            //
            $table->dropColumn('sort');
        });
    }
}
