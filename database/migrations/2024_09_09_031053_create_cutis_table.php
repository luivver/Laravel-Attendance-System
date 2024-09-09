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
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->string('num_cuti'); // tadinya foreignId ganti dengan string
            $table->foreign('num_cuti')
                ->references('employee_num')
                ->on('employees')
                ->onDelete('cascade');
            $table->float('temp_cuti')->nullable();
            $table->float('curr_cuti')->nullable();
            $table->date('exp_temp_cuti')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cutis');
    }
};
