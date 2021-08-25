<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('username',20)->unique();
            $table->string('nickname');
            $table->string('realname',20)->nullable();
            $table->string('email',50)->unique();
            $table->string('phone',11)->unique();
            $table->tinyInteger('sex')->default(1);
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->decimal('money',10,2)->nullable()->default(0.00);
            $table->integer('score')->nullable()->default(0);
            $table->string('qq_openid')->nullable()->default('');
            $table->string('weibo_uid')->nullable()->default('');
            $table->string('wechat_dyh_openid')->nullable()->default('');
            $table->string('wechat_fwh_openid')->nullable()->default('');
            $table->string('wechat_mp_openid')->nullable()->default('');
            $table->string('wechat_unionid')->nullable()->default('');
            $table->string('last_login_ip')->nullable()->default('');
            $table->timestamp('last_login_time')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
