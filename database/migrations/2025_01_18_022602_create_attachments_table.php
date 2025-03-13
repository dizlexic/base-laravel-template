<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('path');
            $table->string('type');
            $table->string('size');
            $table->string('extension');
            $table->string('mime_type');
            $table->string('disk');
            $table->string('visibility');
            $table->string('status');
            $table->string('description')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->string('credit')->nullable();
            $table->string('source')->nullable();
            $table->string('source_url')->nullable();
            $table->timestamps();
        });
    }
};
