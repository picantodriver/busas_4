<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_employee_name_unique_in_signatories_table.php
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
        Schema::table('signatories', function (Blueprint $table) {
            $table->string('employee_name', 255)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signatories', function (Blueprint $table) {
            $table->dropUnique(['employee_name']);
            $table->string('employee_name', 255)->change();
        });
    }
};
