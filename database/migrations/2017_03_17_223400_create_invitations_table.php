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
            $table->unsignedInteger('invited_by');
            $table->string('email')->unique();
            $table->string('token', 128)->unique();
            $table->boolean('is_depleted')->default(false);
            $table->timestamp('depleted_at')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
        });

        //Indexes
        Schema::table($this->table, function (Blueprint $table) {
            $table->index('user_id');
            $table->index('invited_by');
        });

        //Foreign Keys
        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('invited_by')->references('id')->on('users');
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
            $table->dropIndex(['user_id']);
            $table->dropIndex(['invited_by']);
            $table->dropForeign($this->table.'_user_id_foreign');
            $table->dropForeign($this->table.'_invited_by_foreign');
        });

        Schema::dropIfExists($this->table);
    }
}
