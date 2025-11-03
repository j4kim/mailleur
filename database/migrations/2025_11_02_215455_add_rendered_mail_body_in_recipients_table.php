<?php

use App\Models\Recipient;
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
        Schema::table('recipients', function (Blueprint $table) {
            $table->text('rendered_mail_body')->nullable();
        });

        $editor = new \Tiptap\Editor;

        foreach (Recipient::all() as $recipient) {
            $html = $recipient->getRawOriginal('mail_body');
            $recipient->mail_body = $editor->setContent($html)->getDocument();
            $recipient->rendered_mail_body = $html;
            $recipient->save();
        }

        Schema::table('recipients', function (Blueprint $table) {
            $table->json('mail_body')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->text('mail_body')->nullable()->change();
            $table->dropColumn('rendered_mail_body');
        });

        $editor = new \Tiptap\Editor;

        foreach (Recipient::all() as $recipient) {
            $json = $recipient->getRawOriginal('mail_body');
            $recipient->mail_body = $editor->setContent($json)->getHTML();
            $recipient->save();
        }
    }
};
