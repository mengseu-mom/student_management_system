<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->string('class_id')->primary(); 
            $table->string('class_name')->nullable();
            $table->json('teach_days');
            $table->time('start_hour');
            $table->time('end_hour');
            $table->boolean('status');
            $table->unsignedBigInteger('user_id'); 
            $table->timestamps();

            // add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
