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
        Schema::create('students_records', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->decimal('final_grade', 2, 1);
            $table->decimal('removal_rating', 2, 1)->nullable();
            $table->foreignId('student_id')->references('id')->on('students');
            $table->foreignId('course_id')->references('id')->on('courses');
            $table->foreignId('acad_term_id')->references('id')->on('acad_terms');
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_records');
    }
};
