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
        Schema::create('medications', function (Blueprint $table) {
            $table->id('MedicationID')->primary();
            $table->timestamps();
            $table->string('PatientID');
            $table->string('MedicationName');
            $table->string('Type');
            $table->integer('Dosage');
            $table->integer('Frequency');
            $table->date('StartDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
