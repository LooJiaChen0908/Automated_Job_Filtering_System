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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('expected_salary')->nullable();
            $table->integer('work_experience')->nullable();
            $table->integer('specialization')->nullable();
            $table->integer('highest_qualification')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
