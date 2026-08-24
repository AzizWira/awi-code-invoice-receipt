<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('internal_code', 40)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_service_type')->nullable();
            $table->string('project_name');
            $table->text('description')->nullable();
            $table->date('transaction_date');
            $table->string('work_status', 30)->default('menunggu')->index();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('discount_type', 20)->nullable();
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->string('payment_status', 30)->default('belum_dibayar')->index();
            $table->string('transaction_status', 20)->default('draft')->index();
            $table->text('internal_note')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->index(['transaction_date', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
