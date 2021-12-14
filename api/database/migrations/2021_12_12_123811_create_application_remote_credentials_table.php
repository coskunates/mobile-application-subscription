<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationRemoteCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_remote_credentials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('application_id', false, true);
            $table->tinyInteger('os')->comment('1 => google, 2 => ios');
            $table->string('username', 50);
            $table->string('password', 50);
            $table->timestamps();

            $table->foreign('application_id', 'fk_arc_application_id')
                ->references('id')
                ->on('applications')
                ->cascadeOnDelete();
            $table->unique(['application_id', 'os'], 'uidx_application_id_os');
            $table->index(['username', 'password'], 'idx_username_password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_remote_credentials');
    }
}
