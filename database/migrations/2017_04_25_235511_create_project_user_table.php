<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectUserTable extends Migration
{
    protected $table = 'project_user';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('user_id');
            $table->boolean('has_github_team')->default(false);
            $table->boolean('has_taiga_membership')->default(false);
            $table->boolean('is_leader')->default(false);
            $table->primary(['project_id', 'user_id']);
            $table->timestamps();
        });

        //Foreign Keys
        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropForeign($this->table.'_project_id_foreign');
            $table->dropForeign($this->table.'_user_id_foreign');
        });

        Schema::dropIfExists($this->table);
    }
}
