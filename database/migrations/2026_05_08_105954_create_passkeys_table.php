<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Passkeys\Passkeys;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $userModel = Passkeys::userModel();
        $userTable = (new $userModel)->getTable();

        Schema::create('passkeys', function (Blueprint $table) use ($userTable) {
            // API-exposed resource — UUID PK per AGENTS.md §6.3.
            $table->uuid('id')->primary();
            // users.id is a UUID, so this FK must be UUID too.
            $table->foreignUuid('user_id')
                ->constrained(table: $userTable)
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('credential_id')->unique();
            $table->json('credential');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passkeys');
    }
};
