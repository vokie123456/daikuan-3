<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->string('marks', 1000)->default('')->comment('标签(json)')->after('repayments');
            $table->unsignedTinyInteger('isNew')->default(0)->comment('是否最新: 0 非最新, 1 最新')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn('marks');
            $table->dropColumn('isNew');
        });
    }
}
