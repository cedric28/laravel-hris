<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string("product_name")->unique();
            $table->string("generic_name");
            $table->string("unit_measurement");
            $table->string("sku")->unique();
            $table->longText("content");
            $table->longText("image")->nullable();
            $table->integer('supplier_id')->unsigned()->index();
            $table->decimal('original_price', 8, 2)->default(0);
            $table->decimal('selling_price', 8, 2)->default(0);
            $table->integer('quantity');
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
        Schema::dropIfExists('inventories');
    }
}
