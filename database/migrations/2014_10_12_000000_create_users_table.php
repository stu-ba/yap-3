<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->unsignedInteger('taiga_id')->unique()->nullable()->default(null);
            $table->unsignedInteger('github_id')->unique()->nullable()->default(null);
            $table->string('email')->unique()->nullable()->default(null);
            $table->string('username')->unique()->nullable()->default(null);
            $table->string('name')->nullable()->default(null);
            $table->string('bio', 160)->nullable()->default('I am human.');
            $table->string('ban_reason')->nullable()->default('No one invited you.');
            $table->string('avatar')->nullable()->default(null);
            $table->rememberToken();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->boolean('is_confirmed')->default(false);
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
