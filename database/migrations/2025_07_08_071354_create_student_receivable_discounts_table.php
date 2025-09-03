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
        Schema::create('student_receivable_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_receivable_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->unsignedTinyInteger('percent');
            $table->integer('nominal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_receivable_discounts');
    }
};
