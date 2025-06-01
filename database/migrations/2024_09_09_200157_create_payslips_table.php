<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayslipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->integer('deployment_id')->unsigned()->index();
            $table->integer('payroll_id')->unsigned()->index();
            $table->decimal('holiday_pay', 8, 2)->default(0);
            $table->decimal('other_deduction', 8, 2)->default(0);
            $table->decimal('other_pay', 8, 2)->default(0);
            $table->boolean('include_thirteen_month_pay')->default(false);
            $table->decimal('thirteen_month_pay_non_taxable', 8, 2)->default(0);
            $table->decimal('thirteen_month_pay_taxable', 8, 2)->default(0);
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
        Schema::dropIfExists('payslips');
    }
}
