<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_cloth_profile', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->json('profile_data')->nullable();

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
        Schema::dropIfExists('customer_cloth_profile');
    }
};
