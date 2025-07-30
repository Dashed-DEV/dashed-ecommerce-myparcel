<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyParcelOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashed__order_my_parcel', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('dashed__orders')
                ->cascadeOnDelete();
            $table->string('shipment_id')
                ->nullable();
            $table->longText('label')
                ->nullable();
            $table->string('label_url')
                ->nullable();
            $table->json('track_and_trace')
                ->nullable();
            $table->boolean('label_printed')
                ->default(0);

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
        Schema::dropIfExists('dashed__order_my_parcel');
    }
}
