<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_new_column_to_curriculas_table.php
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
        Schema::table('curriculas', function (Blueprint $table) {
            $table->foreignId('program_id')->references('id')->on('programs');
            $table->foreignId('program_major_id')->nullable()->references('id')->on('programs_majors');
             // Add the new column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curriculas', function (Blueprint $table) {
            $table->dropColumn('program_id');
            $table->dropColumn('program_major_id'); // Drop the column if the migration is rolled back
        });
    }
};
