<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_degree_attained_to_students_graduation_infos_table.php
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
        Schema::table('students_graduation_infos', function (Blueprint $table) {
            $table->string('degree_attained', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students_graduation_infos', function (Blueprint $table) {
            $table->dropColumn('degree_attained');
        });
    }
};
