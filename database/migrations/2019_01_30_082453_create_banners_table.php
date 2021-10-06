<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('category_id')->default(0)->nullable();
            $table->string('title');
            $table->tinyInteger('url_type')->comment('url类型1：文章，2：单页，3：分类目录，4：外部链接')->default(1);
            $table->string('url')->comment('例如：https://www.baidu.com，/article/1.html');
            $table->string('cover_id');
            $table->integer('sort')->default(0)->nullable();
            $table->boolean('status')->default(1);
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
}
