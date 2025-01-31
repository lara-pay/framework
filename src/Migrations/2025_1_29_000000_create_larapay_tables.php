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
            $table->string('tag')->default('application');
            $table->string('alias')->unique();
            $table->string('identifier');
            $table->string('namespace');
            $this->enum('type', ['single', 'subscription'])->default('subscription');
            $table->text('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create(config('larapay.tables.payments', 'larapay_payments'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('gateway_id')->nullable()->references('id')->on(config('larapay.tables.gateways', 'larapay_gateways'))->onDelete('set null');
            $table->string('transaction_id')->nullable();
            $table->string('tag')->default('application');
            $table->string('status')->default('unpaid');
            $table->string('description');
            $table->string('currency')->default('USD');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('success_url')->nullable();
            $table->string('cancel_url')->nullable();
            $table->string('handler')->nullable();
            $table->json('data')->nullable();
            $table->json('gateway_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create(config('larapay.tables.subscriptions', 'larapay_subscriptions'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('gateway_id')->nullable()->references('id')->on(config('larapay.tables.gateways', 'larapay_gateways'))->onDelete('set null');
            $table->string('subscription_id')->nullable();
            $table->string('tag')->default('application');
            $table->string('status')->default('inactive');
            $table->string('name');
            $table->string('currency')->default('USD');
            $table->decimal('amount', 10, 2)->default(0);
            $table->integer('frequency')->default(30); // The frequency of the subscription in days
            $table->integer('grace_period')->default(3); // The grace period after the due date in days
            $table->string('success_url')->nullable();
            $table->string('cancel_url')->nullable();
            $table->string('handler')->nullable();
            $table->json('data')->nullable();
            $table->json('gateway_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('larapay.tables.gateways', 'larapay_gateways'));
        Schema::dropIfExists(config('larapay.tables.payments', 'larapay_payments'));
        Schema::dropIfExists(config('larapay.tables.subscriptions', 'larapay_subscriptions'));
    }
};
