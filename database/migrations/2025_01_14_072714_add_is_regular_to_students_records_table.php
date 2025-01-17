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
        Schema::table('students_records', function (Blueprint $table) {
            $table->boolean('is_regular')->default(true); //regular or irregular status of student record
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students_records', function (Blueprint $table) {
            $table->dropColumn('is_regular');
        });
    }
};
