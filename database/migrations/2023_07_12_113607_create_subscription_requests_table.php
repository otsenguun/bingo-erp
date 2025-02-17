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
        Schema::create('subscription_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_updated_by_id')->nullable()->comment('user_id')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('transaction_id')->nullable()->comment('not required, can be bkash, rocket trx id');
            $table->string('document_path')->nullable()->comment('not required, can be any kind of file like pdf, jpg, png, doc');
            $table->unsignedSmallInteger('status')->default(0)->comment('0: Pending, 1: Accepted, 2: Rejected');
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
        Schema::dropIfExists('subscription_requests');
    }
};
