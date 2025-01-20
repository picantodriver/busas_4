<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIsRegularFromStudentRecordsTable extends Migration
{
    public function up()
    {
        Schema::table('students_records', function (Blueprint $table) {
            $table->dropColumn('is_regular');
        });
    }

    public function down()
    {
        Schema::table('students_records', function (Blueprint $table) {
            $table->boolean('is_regular')->default(true);
        });
    }
}
