<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStartDateAndEndDateFromCurriculasTable extends Migration
{
    public function up()
    {
        Schema::table('curriculas', function (Blueprint $table) {
            if (Schema::hasColumn('curriculas', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('curriculas', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }

    public function down()
    {
        Schema::table('curriculas', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }
}
