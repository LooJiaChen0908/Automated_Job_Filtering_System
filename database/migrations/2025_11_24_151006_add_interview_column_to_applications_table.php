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
            $table->string('interview_mode')->nullable()->after('resume_path'); // 'online' or 'f2f'
            $table->dateTime('interview_slot')->nullable()->after('resume_path'); // chosen date+time
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('interview_mode');
            $table->dropColumn('interview_slot');
        });
    }
};
