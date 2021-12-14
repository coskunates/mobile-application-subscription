<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('device_id', false, true);
            $table->bigInteger('application_id', false, true);
            $table->smallInteger('worker_group');
            $table->string('receipt', 32);
            $table->tinyInteger('status');
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'expired_at'], 'idx_status_expired_at');
            $table->foreign('device_id', 'fk_s_device_id')
                ->references('id')
                ->on('devices')
                ->cascadeOnDelete();
            $table->foreign('application_id', 'fk_s_application_id')
                ->references('id')
                ->on('applications')
                ->cascadeOnDelete();
            $table->unique(['device_id', 'application_id'], 'uidx_s_device_id_application_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
