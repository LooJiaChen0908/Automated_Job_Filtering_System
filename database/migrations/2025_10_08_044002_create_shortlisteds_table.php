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
        Schema::create('shortlisteds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->nullable()->constrained('applications')->onDelete('set null');
            $table->string('contact_by')->nullable();
            $table->string('contact_method')->nullable();
            $table->string('notes')->nullable();
            $table->integer('status')->default(0); // 0,-1,1
            $table->timestamp('interview_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortlisteds');
    }
};
