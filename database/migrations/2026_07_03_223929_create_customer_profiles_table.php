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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            
            // Links directly to the user. If the user is deleted, their profile is too.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Vital for PH deliveries (e.g., 09171234567)
            $table->string('phone_number', 15)->nullable(); 
            
            // Philippine standard address structure
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city_municipality')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street_address')->nullable(); // House number, street name
            $table->string('zip_code', 4)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
