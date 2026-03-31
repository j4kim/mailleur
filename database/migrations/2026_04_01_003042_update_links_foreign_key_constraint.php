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
        Schema::table('links', function (Blueprint $table) {
            // Make recipient_id nullable
            $table->unsignedBigInteger('recipient_id')->nullable()->change();
            // Drop the existing foreign key
            $table->dropForeign(['recipient_id']);
            // Add the new foreign key with null on delete
            $table->foreign('recipient_id')->references('id')->on('recipients')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['recipient_id']);
            // Add back the foreign key with cascade on delete
            $table->foreign('recipient_id')->references('id')->on('recipients')->cascadeOnDelete();
            // Make recipient_id not nullable
            $table->unsignedBigInteger('recipient_id')->nullable(false)->change();
        });
    }
};
