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
        Schema::table('transactions', function (Blueprint $table) {
            // Change the stage column from enum to string to support unlimited incoming/outgoing cycles
            $table->string('stage')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert back to enum if rollback is needed
            $table->enum('stage', ['incoming', '2nd incoming', '3rd incoming', 'outgoing', '2nd outgoing', '3rd outgoing'])->change();
        });
    }
};
