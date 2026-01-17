<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path', 500);
            $table->string('media_type')->default('file'); // 'image', 'audio', 'video', 'document', 'file'
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('disk')->default('public');

            // Polymorphic relationship (optional)
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('collection_name')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id', 'collection_name']);
            $table->index('collection_name');
            $table->index('media_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
