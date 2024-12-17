<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_year_unique_in_acad_years_table.php
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
        Schema::table('acad_years', function (Blueprint $table) {
            $table->string('year', 255)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acad_years', function (Blueprint $table) {
            $table->dropUnique(['year']);
            $table->string('year', 255)->change();
        });
    }
};
