<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->enum("always_on_time",['yes','no'])->nullable();
            $table->enum("prompt_and_on_time",['yes','no'])->nullable();
            $table->enum("adheres_to_the_schedule",['yes','no'])->nullable();
            $table->enum("very_reliable_at_work",['yes','no'])->nullable();
            $table->enum("inspires_others_to_improve_attendance",['yes','no'])->nullable();
            $table->enum("is_frequently_late_to_work",['yes','no'])->nullable();
            $table->enum("unreliable_about_reporting",['yes','no'])->nullable();
            $table->enum("unwilling_to_work_beyond_scheduled_hours",['yes','no'])->nullable();
            $table->enum("not_a_dependable_employee",['yes','no'])->nullable();
            $table->enum("work_results_are_inconsistent",['yes','no'])->nullable();
            $table->integer("rate")->nullable();
            $table->integer('deployment_id')->unsigned()->index();
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
        Schema::dropIfExists('feedback');
    }
}
