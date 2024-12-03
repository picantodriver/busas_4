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
        Schema::create('signatories', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('employee_name', 255);           //e.g. Marc Angelo A. Galan
            $table->string('suffix', 10);                   //e.g. PhD.
            $table->string('employee_designation', 100);    //e.g. University Registrar, Registrar IV
            $table->boolean('status');                              //e.g. 1 - Permanent, 0 - COS/JO
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
        Schema::dropIfExists('signatories');
    }
};
