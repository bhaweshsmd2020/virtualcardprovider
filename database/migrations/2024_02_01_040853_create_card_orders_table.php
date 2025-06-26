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
        Schema::create('card_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable();
            $table->string('payment_id')->nullable();
            $table->foreignId('card_id')->constrained('cards')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gateway_id')->constrained('gateways')->cascadeOnDelete();
            $table->uuid('order_uid')->nullable();
            $table->double('amount')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'canceled', 'rejected'])->default('unpaid');
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'inactive'])->default('pending');
            $table->ipAddress();
            $table->string('deposit_fee')->default(0);
            $table->text('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_orders');
    }
};
