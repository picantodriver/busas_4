<?php
// database/migrations/xxxx_xx_xx_xxxxxx_remove_nstp_number_from_students_graduation_infos_table.php
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
            $table->dropColumn('nstp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students_graduation_infos', function (Blueprint $table) {
            $table->string('nstp_number', 255)->nullable();
        });
    }
};
