<?php
// database/migrations/xxxx_xx_xx_xxxxxx_drop_start_date_and_end_date_from_curriculas_table.php
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
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curriculas', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }
};
