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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('income_account_id')->constrained('accounts')->onDelete('cascade');
            $table->enum('user_type', ['Siswa', 'Guru', 'Karyawan']);
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->index(['school_id', 'account_id', 'income_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
