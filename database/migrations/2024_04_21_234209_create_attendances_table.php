<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->integer('deployment_id')->unsigned()->index();
            $table->time('attendance_time')->default(date("H:i:s"));
            $table->time('attendance_out')->default(date("H:i:s"));
            $table->date('attendance_date')->default(date("Y-m-d"));
            $table->enum('day_of_week', ['0', '1', '2', '3', '4', '5', '6'])->default(0);
            $table->enum('status',['Present', 'Absent'])->default('Present');
            $table->integer('hours_worked')->default(0);
            $table->integer('creator_id')->unsigned()->index();
            $table->integer('updater_id')->unsigned()->index();
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
        Schema::dropIfExists('attendances');
    }
}
