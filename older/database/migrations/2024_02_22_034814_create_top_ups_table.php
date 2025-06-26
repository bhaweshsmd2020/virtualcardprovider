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
        Schema::create('top_ups', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_uid')->nullable();
            $table->string('invoice_no')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('gateway_id')->constrained('gateways')->cascadeOnDelete();
            $table->string('payment_id')->nullable();
            $table->string('type')->default('balance');
            $table->double('amount')->nullable();
            $table->string('requested_amount')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'canceled', 'rejected'])->default('unpaid');
            $table->string('transaction_fee')->default(0);
            $table->string('rate')->default(1);
            $table->text('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_ups');
    }
};
