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
        Schema::create('virtual_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('preview')->nullable();
            $table->string('currency');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('precision')->default(2);
            $table->integer('is_default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_currencies');
    }
};
