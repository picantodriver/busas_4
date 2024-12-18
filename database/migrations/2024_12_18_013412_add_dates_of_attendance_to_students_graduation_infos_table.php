<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_dates_of_attendance_to_students_graduation_infos_table.php
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
            $table->string('dates_of_attendance', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students_graduation_infos', function (Blueprint $table) {
            $table->dropColumn('dates_of_attendance');
        });
    }
};
