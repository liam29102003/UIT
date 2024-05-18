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
        Schema::create('committe_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rank');
            $table->string('position')->nullable();
            $table->foreignId('conference_id')->constrained();
            $table->enum('speaker_type',['keynote','invited','none']);
            $table->enum('member_type',['organizing','program','none']);
            $table->enum('chair_type',['general chair', 'general co-chair', 'program chair','none']);
            $table->string('nation');
            $table->string('university');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committe_members');
    }
};
