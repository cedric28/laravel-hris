<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->integer('employee_id')->unsigned()->index();
            $table->integer('employment_type_id')->unsigned()->index();
            $table->integer('client_id')->unsigned()->index();
            $table->string("position");
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum("status",['new','end','terminate','regular'])->default('new');
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
        Schema::dropIfExists('deployments');
    }
}
