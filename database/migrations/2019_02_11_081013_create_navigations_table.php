<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigations', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('pid');
            $table->integer('uid');
            $table->string('title');
            $table->integer('cover_id');
            $table->tinyInteger('url_type')->comment('url类型1：文章，2：单页，3：分类目录，4：外部链接')->default(3);
            $table->string('url')->comment('例如：https://www.baidu.com，/article/1.html');
            $table->integer('sort');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('navigations');
    }
}
