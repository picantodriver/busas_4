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
        Schema::create('students_graduation_infos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->date('graduation_date');
            $table->string('board_approval', 255);
            $table->string('latin_honor', 100);
            $table->string('nstp_number', 100);
            $table->decimal('gwa', 5, 2);
            $table->foreignId('student_id')->references('id')->on('students');
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_graduation_infos');
    }
};
