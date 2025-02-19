<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ladderized', function (Blueprint $table) {
            $table->id();
            $table->string('board_approval');
            $table->string('latin_honor')->nullable();
            $table->string('program_cert');
            $table->date('graduation_date');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ladderized');
    }
};
