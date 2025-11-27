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
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('interview_slot');

            // Add new flexible columns
            $table->json('interview_slots')->nullable()->after('interview_mode');   // admin proposed slots
            $table->dateTime('selected_slot')->nullable()->after('interview_slots');   // applicant's choice
            $table->json('suggested_slots')->nullable()->after('selected_slot');   // applicant's suggestions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Rollback: remove new columns
            $table->dropColumn(['interview_slots', 'selected_slot', 'suggested_slots']);

            // Restore old column
            $table->string('interview_slot')->nullable();
        });
    }
};
