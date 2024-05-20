<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOverTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('over_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deployment_id')->index();
            $table->time('duration');
            $table->time('overtime_in')->default(date("H:i:s"));
            $table->time('overtime_out')->default(date("H:i:s"));
            $table->date('overtime_date');
            $table->unsignedBigInteger('attendance_id')->index();
            $table->unsignedBigInteger('creator_id')->index();
            $table->unsignedBigInteger('updater_id')->index();
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('over_times');
    }
}
