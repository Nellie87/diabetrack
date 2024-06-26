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
        Schema::create('patients', function (Blueprint $table) {
            $table->id()->primary();
            $table->timestamps();
            $table->date('DateOfBirth');
            $table->string('Gender');
            $table->string('PhoneNo');
            $table->string('Address');
            $table->string('EmergencyContactName');
            $table->string('EmergencyContactPhone');
            $table->string('DoctorID')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
