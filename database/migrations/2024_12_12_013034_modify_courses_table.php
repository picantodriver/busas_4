<?php
// database/migrations/xxxx_xx_xx_xxxxxx_modify_courses_table.php
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
        Schema::table('courses', function (Blueprint $table) {
            // Remove the foreign keys
            $table->dropForeign(['program_id']);
            $table->dropColumn('program_id');
            $table->dropForeign(['program_major_id']);
            $table->dropColumn('program_major_id');

            // Add the new foreign key
            $table->foreignId('curricula_id')->constrained('curriculas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add the foreign keys back
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('program_major_id')->constrained('programs_majors')->onDelete('cascade');

            // Remove the new foreign key
            $table->dropForeign(['curricula_id']);
            $table->dropColumn('curricula_id');
        });
    }
};
