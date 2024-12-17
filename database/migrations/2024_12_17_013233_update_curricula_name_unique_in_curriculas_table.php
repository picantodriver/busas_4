<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_curricula_name_unique_in_curriculas_table.php
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
            $table->string('curricula_name', 255)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curriculas', function (Blueprint $table) {
            $table->dropUnique(['curricula_name']);
            $table->string('curricula_name', 255)->change();
        });
    }
};
