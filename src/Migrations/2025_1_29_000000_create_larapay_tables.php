<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('larapay.tables.gateways', 'larapay_gateways'), function (Blueprint $table) {
            $table->id();
            $table->string('alias')->unique();
            $table->string('identifier');
            $table->string('namespace');
            $table->string('tag')->default('application');
            $table->text('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create(config('larapay.tables.payments', 'larapay_payments'), function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('gateway_id')->nullable()->references('id')->on(config('larapay.tables.gateways', 'larapay_gateways'))->onDelete('set null');
            $table->string('description')->nullable();
            $table->string('status')->default('unpaid');
            $table->string('currency')->default('USD');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('transaction_id')->nullable();
            $table->string('success_url')->nullable();
            $table->string('cancel_url')->nullable();
            $table->string('handler')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('larapay.tables.gateways', 'larapay_gateways'));
        Schema::dropIfExists(config('larapay.tables.payments', 'larapay_payments'));

    }
};
