<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CreateDeliveryRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_request_items', function (Blueprint $table) {
            $table->id();
            $table->integer('delivery_request_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->integer("qty");
            $table->longText("note")->nullable();
            $table->integer("received_qty")->default(0);
            $table->integer("defectived_qty")->default(0);
            $table->date('expired_at')->default(Carbon::now());
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
        Schema::dropIfExists('delivery_request_items');
    }
}
