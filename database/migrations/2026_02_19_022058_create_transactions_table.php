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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->enum('stage', ['incoming', '2nd incoming', '3rd incoming', 'outgoing', '2nd outgoing', '3rd outgoing']);
            $table->string('department');
            $table->dateTime('date_out')->nullable();
            $table->string('received_by')->nullable();
            $table->enum('status', ['pending', 'complete'])->default('pending');
            $table->dateTime('date_in')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
