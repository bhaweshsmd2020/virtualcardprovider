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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('username')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->string('role')->default('user');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            
            $table->tinyInteger('dob_day')->nullable();
            $table->tinyInteger('dob_month')->nullable();
            $table->smallInteger('dob_year')->nullable();

            $table->string('country_code')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('postal_code')->nullable();
            $table->string('address_line', 255)->nullable();

            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            $table->longText('plan_data')->nullable();
            $table->integer('plan_id')->nullable()->constrained();
            $table->date('will_expire')->nullable();
            $table->double('balance')->default(0);

            $table->integer('status')->default(1);
            $table->string('password')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('kyc_verified_at')->nullable();

            $table->text('google2fa_secret')->nullable();
            $table->text('google2fa_ts')->nullable();
            
            $table->text('meta')->nullable();
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
