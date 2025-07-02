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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('employee_id_number')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->datetime('deleted_at')->nullable();
            $table->timestamps();
            $table->index(['school_id', 'employee_id_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
