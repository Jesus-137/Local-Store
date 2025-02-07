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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email');
            $table->string('phone_code');
            $table->string('phone_number');
            $table->string('state');
            $table->string('address');
            $table->date('birth_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('phone_number');
            $table->index('email');
            $table->index(['first_name', 'last_name']);

            $table->unique(['email', 'user_id']);

            $table->fullText(['first_name', 'last_name', 'middle_name', 'email']);
            $table->fullText(['notes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
