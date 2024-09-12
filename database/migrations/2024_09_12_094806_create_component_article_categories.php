<?php

use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasCustomMigration;


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('component_article_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('articleId')->constrained('articles')->onDelete('cascade');
            $table->foreignId('categoryId')->constrained('component_categories')->onDelete('cascade');

            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_article_categories');
    }
};
