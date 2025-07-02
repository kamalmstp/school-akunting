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
        Schema::table('accounts', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable();
        });

        Schema::table('beginning_balances', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable();
        });

        Schema::table('fix_assets', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable();
        });

        Schema::table('student_receivables', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('beginning_balances', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('fix_assets', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('student_receivables', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
