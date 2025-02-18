<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('students_records', function (Blueprint $table) {
            // Add columns if they don't already exist
            if (!Schema::hasColumn('students_records', 'course_code')) {
                $table->string('course_code')->after('course_id')->nullable();
            }
            if (!Schema::hasColumn('students_records', 'descriptive_title')) {
                $table->string('descriptive_title')->after('course_code')->nullable();
            }
            if (!Schema::hasColumn('students_records', 'curricula_name')) {
                $table->string('curricula_name')->after('curricula_id')->nullable();
            }
            if (!Schema::hasColumn('students_records', 'course_unit')) {
                $table->decimal('course_unit', 8, 2)->after('removal_rating')->nullable();
            }

            // Modify columns only if they exist
            if (Schema::hasColumn('students_records', 'final_grade')) {
                $table->decimal('final_grade', 5, 2)->nullable()->change();
            }
            if (Schema::hasColumn('students_records', 'acad_term_id')) {
                $table->bigInteger('acad_term_id')->unsigned()->nullable()->change();
            }
            if (!Schema::hasColumn('students_records', 'attachments')) {
                $table->json('attachments')->nullable()->after('course_unit');
            }
        });
    }

    public function down()
    {
        Schema::table('students_records', function (Blueprint $table) {
            $table->dropColumn(['course_code', 'descriptive_title', 'curricula_name', 'course_unit']);
            if (Schema::hasColumn('students_records', 'final_grade')) {
                $table->string('final_grade')->change();
            }
            if (Schema::hasColumn('students_records', 'acad_term_id')) {
                $table->bigInteger('acad_term_id')->unsigned()->default(1)->change();
            }
        });
    }
};
