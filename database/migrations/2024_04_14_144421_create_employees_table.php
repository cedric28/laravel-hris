<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string("name")->unique();
            $table->string("nickname");
            $table->integer('gender_id')->unsigned()->index();
            $table->integer('civil_status_id')->unsigned()->index();
            $table->date("birthdate");
            $table->longText("address");
            $table->string("contact_number");
            $table->string('email');
            $table->string('sss')->nullable();
            $table->string('pagibig')->nullable();
            $table->string('tin')->nullable();
            $table->string('philhealth')->nullable();
            $table->string("emergency_contact_name")->nullable();
            $table->longText("emergency_relationship")->nullable();
            $table->string("emergency_contact_number")->nullable();
            $table->longText("emergency_address")->nullable();
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
        Schema::dropIfExists('employees');
    }
}
