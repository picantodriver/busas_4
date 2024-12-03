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
        Schema::create('curriculas', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->foreignId('acad_year_id')->references('id')->on('acad_years');
            $table->string('curricula_name', 255);
            //$table->date('start_date'); column dropped
            //$table->date('end_date'); column dropped
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculas');
    }
};
