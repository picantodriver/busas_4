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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->foreignId('college_id')->references('id')->on('colleges');
            $table->string('program_name', 255);                //e.g. Bachelor of Science in Computer Science, Bachelor of Science in Accountancy
            $table->string('program_abbreviation', 20);         //e.g. BSCS, BSA
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
        Schema::dropIfExists('programs');
    }
};
