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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('conference_date');
            $table->longText('description'); // call for paper or about conference
            $table->string('key_note_speaker');   
            $table->string('invited_speaker');   
            $table->longText('topics'); // may be list of conference
            $table->string('general_chair');// committe
            $table->string('co_chair');
            $table->string('program_chair');
            $table->longText('paper_sub_guide');
            $table->string('sub_deadline');
            $table->string('updated_sub_deadline');
            $table->string('accept_noti');
            $table->string('normal_fee');
            $table->string('early_bird_fee');
            $table->string('local_fee');
            $table->string('sub_email');
            $table->string('camera_ready');
            $table->string('brochure');
            $table->string('book');
            $table->timestamps();
        });
    }

// speaker table
// conference image table


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
