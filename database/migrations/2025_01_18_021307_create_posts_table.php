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
        Schema::dropIfExists('posts');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('content');
            $table->string('slug')->unique();
            $table->string('status')->default('draft');
            $table->string('type')->default('post');
            $table->foreignUuid('user_id')->constrained('users');

            $table->timestamps();

            $table->index('slug');
            $table->index('status');
            $table->index('type');
        });
    }
};
