<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->integer('deployment_id')->unsigned()->index();
            $table->decimal('basic_salary', 8, 2)->default(479);
            $table->enum('rate_base',['hourly','monthly'])->default('hourly');
            $table->decimal('sss', 8, 2)->default(560.43);
            $table->decimal('tax', 8, 2)->default(0.02);
            $table->decimal('tax_salary_range', 8, 2)->default(21000);
            $table->decimal('pagibig', 8, 2)->default(100);
            $table->decimal('philhealth', 8, 2)->default(498.16);
            $table->decimal('uniform', 8, 2)->default(0);
            $table->decimal('meal_allowance', 8, 2)->default(0);
            $table->decimal('laundry_allowance', 8, 2)->default(0);
            $table->decimal('transportation_allowance', 8, 2)->default(0);
            $table->decimal('cola', 8, 2)->default(150);
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
        Schema::dropIfExists('salaries');
    }
}
