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
        Schema::create('diets', function (Blueprint $table) {
            $table->id('DietID')->primary();
            $table->timestamps();
            $table->string('PatientID');
            $table->date('Date');
            $table->string('MealType');
            $table->text('FoodItems');
            $table->string('Carbohydrates');
            $table->text('Notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet');
    }
};
