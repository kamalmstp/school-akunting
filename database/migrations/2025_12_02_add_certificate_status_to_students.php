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
        Schema::table('students', function (Blueprint $table) {
            $table->enum('certificate_status', ['not_taken', 'taken'])->default('not_taken')->after('notes')->comment('Status pengambilan ijazah: not_taken = belum diambil, taken = sudah diambil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('certificate_status');
        });
    }
};
