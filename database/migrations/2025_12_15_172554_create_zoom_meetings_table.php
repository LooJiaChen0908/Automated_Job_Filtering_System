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
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                  ->constrained('applications')
                  ->onDelete('cascade');

            $table->string('uuid')->nullable();
            $table->string('zoom_meeting_id')->unique(); // Zoom's meeting ID
            $table->string('host_id')->nullable();
            $table->string('host_email')->nullable();

            $table->string('topic')->nullable();
            $table->tinyInteger('type')->nullable(); // 1=Instant, 2=Scheduled, 3=Recurring, 8=Recurring fixed
            $table->string('status')->nullable();

            $table->dateTime('start_time')->nullable();
            $table->integer('duration')->nullable();
            $table->string('timezone')->nullable();

            $table->string('join_url')->nullable();   // safe to share
            $table->string('start_url')->nullable();  // host only, keep secure
            $table->string('password')->nullable();

            $table->json('settings')->nullable(); // optional: store Zoom settings block

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meetings');
    }
};
