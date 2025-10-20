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
        Schema::create('attendences', function (Blueprint $table) {
            $table->id();
           $table->string('student_id'); // must match data type of referenced column
            $table->foreign('student_id')
                  ->references('student_id')
                  ->on('student_lists')
                  ->onDelete('cascade');
            $table->date('date');
            $table->enum('status',['Present','Absent','Late'])->default('Present');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendences');
    }
};
