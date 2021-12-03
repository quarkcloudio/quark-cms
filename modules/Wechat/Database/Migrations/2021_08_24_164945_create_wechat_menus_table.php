<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_menus', function (Blueprint $table) {
            $table->id();
            $table->integer('pid');
            $table->string('wechat_type')->comment('DYH,FWH');
            $table->string('name');
            $table->string('type')->nullable()->comment('view等');
            $table->string('value')->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('wechat_menus');
    }
}
