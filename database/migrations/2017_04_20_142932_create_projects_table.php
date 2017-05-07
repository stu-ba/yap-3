<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    protected $table = 'projects';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('github_team_id')->unique()->nullable()->default(null);
            $table->unsignedInteger('github_repository_id')->unique()->nullable()->default(null);
            $table->unsignedInteger('taiga_id')->unique()->nullable()->default(null);
            $table->unsignedInteger('project_type_id');
            $table->string('name');
            $table->text('description');
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archive_at')->nullable();
            $table->timestamps();
        });

        //Foreign Keys
        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign('project_type_id')->references('id')->on('project_types');
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
            $table->dropForeign($this->table.'_project_type_id_foreign');
        });

        Schema::dropIfExists($this->table);
    }
}
