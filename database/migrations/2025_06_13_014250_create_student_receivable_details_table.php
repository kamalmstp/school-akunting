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
        Schema::create('student_receivable_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_receivable_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->date('period');
            $table->timestamps();
            $table->index(['student_receivable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_receivable_details');
    }
};
