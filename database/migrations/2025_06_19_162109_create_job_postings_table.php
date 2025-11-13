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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('work_location')->nullable();
            $table->string('work_mode')->nullable();
            $table->string('employment_type')->nullable();
            $table->integer('required_experience_years')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('status')->default(0); // need change active, unactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
