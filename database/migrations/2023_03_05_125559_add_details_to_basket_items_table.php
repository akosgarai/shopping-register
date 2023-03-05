<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\QuantityUnit;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // select the default quantity unit
        $defaultQuantityUnit = QuantityUnit::where('name', QuantityUnit::UNIT_PCS)->first();
        Schema::table('basket_items', function (Blueprint $table) use ($defaultQuantityUnit) {
            $table->decimal('quantity', 8, 3)->default(1);
            $table->foreignId('quantity_unit_id')->default($defaultQuantityUnit->id)->constrained();
            $table->decimal('unit_price', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basket_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->dropForeign(['quantity_unit_id']);
            $table->dropColumn('quantity_unit_id');
            $table->dropColumn('unit_price');
        });
    }
};
