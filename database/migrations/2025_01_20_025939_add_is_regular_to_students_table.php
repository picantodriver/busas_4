<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRegularToStudentsTable extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('is_regular')->default(true);
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('is_regular');
        });
    }
}
