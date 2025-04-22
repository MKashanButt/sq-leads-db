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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('patient_phone', 15);
            $table->string('secondary_phone', 15)->nullable();
            $table->string('first_name', 15);
            $table->string('last_name', 15);
            $table->date('dob');
            $table->string('medicare_id', 15)->unique();
            $table->text('address');
            $table->string('city', 15);
            $table->string('state', 15);
            $table->string('zip', 15);
            $table->text('product_specs');
            $table->string('doctor_name', 30);
            $table->string('patient_last_visit', 20);
            $table->text('doctor_address')->nullable();
            $table->string('doctor_phone', 15);
            $table->string('doctor_fax', 20);
            $table->string('doctor_npi', 50);
            $table->text('recording_link');
            $table->text('comments')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onDelete('SET NULL')->default(1);
            $table->foreignId('insurance_id')->nullable()->constrained('insurances')->onDelete('SET NULL');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('SET NULL');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('SET NULL');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->onDelete('SET NULL');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
