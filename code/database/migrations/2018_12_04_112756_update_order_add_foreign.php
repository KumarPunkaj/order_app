<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderAddForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('orders', function(Blueprint $table) {
            $table->foreign('distance_id')->references('id')->on('distance')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

         Schema::table('orders', function(Blueprint $table) {
            $table->dropForeign('order_distance_id'); //
        });
    }
}
