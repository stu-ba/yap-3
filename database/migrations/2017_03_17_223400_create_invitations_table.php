<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
    protected $table = 'invitations';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('created_by');
            $table->string('email')->unique();
            $table->string('token', 128)->unique();
            $table->boolean('is_depleted')->default(false);
            $table->timestamp('depleted_at')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
        });

        //Foreign Keys
        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
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
            $table->dropForeign($this->table.'_user_id_foreign');
            $table->dropForeign($this->table.'_created_by_foreign');
        });

        Schema::dropIfExists($this->table);
    }
}
