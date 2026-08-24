<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->index();
            $table->string('document_number', 60)->unique();
            $table->char('public_token_hash', 64)->unique();
            $table->text('public_token_encrypted');
            $table->string('public_token_last4', 4);
            $table->string('status', 20)->default('active')->index();
            $table->json('snapshot_json');
            $table->timestamp('generated_at');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['transaction_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
