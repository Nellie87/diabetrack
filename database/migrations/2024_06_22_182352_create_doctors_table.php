<?php

// database/migrations/2024_06_22_123456_create_doctors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('specialization')->nullable();
            $table->string('license_number')->nullable();
            $table->string('institution')->nullable();
            $table->integer('graduation_year')->nullable();
            $table->string('id_document')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('medical_license_card')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctors');
    }
}
