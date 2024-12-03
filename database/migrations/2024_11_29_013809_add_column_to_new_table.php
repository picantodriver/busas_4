<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToNewTable extends Migration
{
    public function up()
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->string('college_address'); // Adjust the column type as needed
        });
    }

    public function down()
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropColumn('college_address');
        });
    }
}
