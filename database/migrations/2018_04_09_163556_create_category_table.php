<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 45)->comment('名称');
            $table->unsignedTinyInteger('type')->default(0)->comment('类型: 0 首页, 1 首页图标, 2 贷款, 3 秒放款');
            $table->string('image', 255)->default('')->comment('图片');
            $table->integer('sort')->unsigned()->default(0)->comment('排序序号');
            $table->unsignedTinyInteger('sort_app')->default(0)->comment('分类内app的排序: 0 时间降序, 1 时间升序, 2 序号降序, 3 序号升序');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态: 0 禁止, 1 正常');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category');
    }
}
