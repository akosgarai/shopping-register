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
        Schema::create('baskets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('shop_id')->constrained()->nullable(false);
            $table->foreignId('user_id')->constrained()->nullable(false);
            $table->dateTime('date')->nullable(false);
            $table->decimal('total', 8, 2)->nullable(false);
            $table->string('receipt_id')->nullable(false);
            $table->string('receipt_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baskets');
    }
};
