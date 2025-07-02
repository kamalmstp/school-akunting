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
        Schema::create('employee_receivable_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_receivable_id')->constrained('employee_receivables')->onDelete('cascade');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->index(['employee_receivable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_receivable_details');
    }
};
