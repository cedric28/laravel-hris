<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->string("reference_no",50)->unique();
            $table->longText("content");
            $table->date('delivery_at');
            $table->string("status")->default('pending');
            $table->string("received_by",100)->default("")->nullable();
            $table->string("vehicle",100)->default("")->nullable();
            $table->string("vehicle_plate",100)->default("")->nullable();
            $table->string("driver_name",100)->default("")->nullable();
            $table->string("contact_number")->default("")->nullable();
            $table->integer('supplier_id')->unsigned()->index();
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
        Schema::dropIfExists('delivery_requests');
    }
}
