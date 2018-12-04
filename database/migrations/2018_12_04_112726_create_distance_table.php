<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distance', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('initial_latitude', 10, 7);
            $table->decimal('initial_longitude', 10, 7);
            $table->decimal('final_latitude', 10, 7);
            $table->decimal('final_longitude', 10, 7);

            // $table->char('initial_latitude', 25);
            // $table->char('initial_longitude', 25);
            // $table->char('final_latitude', 25);
            // $table->char('final_longitude', 25);
            $table->integer('distance'); //Distance in meters
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
        Schema::dropIfExists('distance');
    }
}
