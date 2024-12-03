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
        Schema::create('students_registration_infos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('last_school_attended', 255);
            $table->string('last_year_attended', 4);
            $table->string('category', 100);
            $table->softDeletes();
            $table->foreignId('student_id')->constrained('students', 'id');
            $table->foreignId('acad_term_id')->constrained('acad_terms', 'id');
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_registration_infos');
    }
};
