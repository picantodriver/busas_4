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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('descriptive_title', 255); //e.g. Introduction to Computing
            $table->string('course_code', 20);      //e.g. IT 102
            $table->string('course_unit', 5);       //e.g. 3 or (3)
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
            $table->foreignId('program_id')->constrained('programs', 'id');
            $table->foreignId('program_major_id')->constrained('programs_majors', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
