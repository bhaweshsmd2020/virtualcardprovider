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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_uid')->nullable();
            $table->string('invoice_no')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('credit_card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gateway_id')->nullable()->constrained('gateways')->cascadeOnDelete();
            $table->string('payment_id')->nullable();
            $table->double('amount')->nullable();
            $table->string('requested_amount')->nullable();
            $table->double('tax')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'canceled', 'rejected'])->default('unpaid');
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
        Schema::dropIfExists('deposits');
    }
};
