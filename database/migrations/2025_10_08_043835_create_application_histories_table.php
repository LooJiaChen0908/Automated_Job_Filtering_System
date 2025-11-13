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
        Schema::create('application_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->nullable()->constrained('applications')->onDelete('set null');
            $table->foreignId('applicant_id')->nullable()->constrained('applicants')->onDelete('set null');
            $table->integer('old_status')->nullable();
            $table->integer('new_status')->nullable();
            $table->string('notes')->nullable();
            $table->string('changed_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_histories');
    }
};
