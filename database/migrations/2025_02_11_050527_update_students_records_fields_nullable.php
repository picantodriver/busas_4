<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students_records', function (Blueprint $table) {
            // Make final_grade nullable
            $table->string('final_grade')->nullable()->change();
            
            // Also make sure course_code and descriptive_title are nullable
            // since they might not be available immediately
            $table->string('course_code')->nullable()->change();
            $table->string('descriptive_title')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('students_records', function (Blueprint $table) {
            // Revert changes
            $table->string('final_grade')->nullable(false)->change();
            $table->string('course_code')->nullable(false)->change();
            $table->string('descriptive_title')->nullable(false)->change();
        });
    }
};