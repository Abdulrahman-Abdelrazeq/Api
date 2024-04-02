<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rents_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rents_offer_id');
            $table->foreign('rents_offer_id')->references('id')->on('rents_offers')->onDelete('cascade');
            $table->string('transaction_id');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rents_payments');
    }
};
