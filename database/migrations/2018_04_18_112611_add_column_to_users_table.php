<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('recomm_type')->default(0)->comment('推荐方式: 0 无, 1 好友, 2 公司')->after('address');
            $table->integer('recomm_id')->unsigned()->nullable()->comment('推荐方id')->after('recomm_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('recomm_type');
            $table->dropColumn('recomm_id');
        });
    }
}
