<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// PLATFORM-002 Part 7 — Knowledge Center articles (FAQ, manual, context help).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 150);
            $table->string('locale', 5)->default('en');
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('category', 50)->default('general'); // faq | manual | getting_started | ...
            $table->string('title', 200);
            $table->text('body');                                // Markdown
            $table->string('video_url', 500)->nullable();        // placeholder for future videos
            $table->string('context_key', 100)->nullable();      // screen hook, e.g. 'members.index'
            $table->boolean('published')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['slug', 'locale', 'version']);
            $table->index(['context_key', 'locale', 'published']);
            $table->index(['category', 'published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_articles');
    }
};
