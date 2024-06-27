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
        Schema::create('glucose_readings', function (Blueprint $table) {
            $table->id('ReadingID')->primary();
            $table->timestamps();
            $table->string('PatientID');
            $table->date('Date');
            $table->time('Time');
            $table->float('GlucoseLevel');
            $table->text('Notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glucosereadings');
    }
};
