<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_stock_items', function (Blueprint $table) {
            $table->id();
            $table->integer('return_stock_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->integer("qty");
            $table->string("remark")->nullable();
            $table->longText("note")->nullable();
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
        Schema::dropIfExists('return_stock_items');
    }
}
