<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    protected $table = 'users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('taiga_id')->unique()->nullable()->default(null);
            $table->integer('github_id')->unique()->nullable()->default(null);
            $table->string('email')->unique()->nullable()->default(null);
            $table->string('username')->unique()->nullable()->default(null);
            $table->string('name')->nullable()->default(null);
            $table->string('bio', 160)->nullable()->default('I am human.');
            $table->string('avatar')->nullable()->default(null);
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists($this->table);
    }
}
