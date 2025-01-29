<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('larapay_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('alias')->unique();
            $table->string('identifier');
            $table->string('namespace');
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('larapay_payments', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreign('gateway_id')->nullable()->references('id')->on('larapay_gateways')->onDelete('set null');
            $table->string('description')->nullable();
            $table->string('status')->default('unpaid');
            $table->string('currency')->default('USD');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('transaction_id')->nullable();
            $table->string('handler')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('larapay_gateways');
        Schema::dropIfExists('larapay_payments');

    }
};
