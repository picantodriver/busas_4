<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnFromOriginalTable extends Migration
{
    public function up()
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropColumn('campus_address');
        });
    }

    public function down()
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->string('campus_address'); // Adjust the column type as needed
        });
    }
}
