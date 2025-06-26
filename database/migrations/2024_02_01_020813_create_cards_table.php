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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title');
            $table->double('min_deposit');
            $table->double('required_balance');
            $table->text('description');
            $table->string('preview');
            $table->string('type')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->string('card_variant')->default('basic');
            $table->string('category_type')->nullable();
            $table->text('categories')->nullable();
            $table->integer('is_featured')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
