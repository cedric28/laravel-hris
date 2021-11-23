<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('or_no')->unique;
            $table->string("customer_fullname")->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_rate',10,2);
            $table->decimal('total_discount', 10, 2);
            $table->decimal('total_amount_due', 10, 2);
            $table->decimal('cash_tendered', 10, 2);
            $table->decimal('cash_change', 10, 2);
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
        Schema::dropIfExists('sales');
    }
}
