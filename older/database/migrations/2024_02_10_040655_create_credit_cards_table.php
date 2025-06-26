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
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('card_holder_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('card_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
            $table->text('card');
            $table->decimal('spending_limits', 10, 2)->default(0);
            $table->enum('address_type', ['app', 'user'])->default('user');
            $table->enum('status', ['active', 'inactive']);
            $table->string('exp_month');
            $table->string('exp_year');
            $table->string('last4');
            $table->string('currency')->default('usd');
            $table->text('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
