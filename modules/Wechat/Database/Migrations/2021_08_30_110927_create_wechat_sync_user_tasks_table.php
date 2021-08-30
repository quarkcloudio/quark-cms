<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatSyncUserTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_sync_user_tasks', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('type')->comment('DYH,FWH,MP');
            $table->integer('total_num');
            $table->integer('num');
            $table->string('start_openid')->comment('本次同步开始openid的节点');
            $table->string('last_openid')->comment('本次同步最后更新的openid节点');
            $table->boolean('status')->default(1);
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_sync_user_tasks');
    }
}
