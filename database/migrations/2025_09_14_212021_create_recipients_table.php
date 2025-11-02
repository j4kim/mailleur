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
        Schema::create('recipients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->unique(['campaign_id', 'email']);
            $table->json('data')->nullable();
            $table->text('mail_body')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('failed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipients');
    }
};
