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
        Schema::table('vat_rates', function (Blueprint $table) {
            $table->boolean('is_group_tax')->default(false);
            $table->json('group_tax_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vat_rates', function (Blueprint $table) {
            $table->dropColumn('is_group_tax');
            $table->dropColumn('group_tax_ids');
        });
    }
};
