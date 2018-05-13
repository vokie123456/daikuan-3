<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('apps', function (Blueprint $table) {
            $table->integer('company_id')->unsigned()->comment('归属公司')->nullable()->change();
            $table->string('note', 255)->default('')->comment('备注')->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('apps', function (Blueprint $table) {
            $table->integer('company_id')->unsigned()->comment('归属公司')->change();
            $table->dropColumn('note');
        });
    }
}
