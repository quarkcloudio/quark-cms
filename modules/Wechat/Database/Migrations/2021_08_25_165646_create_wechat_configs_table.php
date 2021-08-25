<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_configs', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('title');
            $table->string('type');
            $table->string('name');
            $table->string('group_name');
            $table->longText('value')->nullable();
            $table->longText('remark')->nullable();
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
        Schema::dropIfExists('wechat_configs');
    }
}
