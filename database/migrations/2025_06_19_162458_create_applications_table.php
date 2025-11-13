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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable()->constrained('applicants')->onDelete('set null');
            $table->foreignId('job_id')->nullable()->constrained('job_postings')->onDelete('set null');
            $table->integer('status')->default(0); // need change ., 0 = pending, 1 = shortlisted, 2 = interview, 3 = offer, -1 = rejected
            $table->string('resume_path')->nullable();
            $table->string('status_reason')->nullable();
            $table->timestamps();
            // php artisan make:migration add_column_name_to_table_name_table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
