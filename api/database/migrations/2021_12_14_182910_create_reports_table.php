<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('application_id', false, true);
            $table->date('date');
            $table->string('event', 20);
            $table->tinyInteger('os')->comment('1 => google, 2 => ios');
            $table->integer('count')->default(0);
            $table->timestamps();

            $table->unique(['application_id', 'date', 'os', 'event'], 'uidx_application_id_date_os_event');
            $table->foreign('application_id', 'fk_r_application_id')
                ->references('id')
                ->on('applications')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report');
    }
}
