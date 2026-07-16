<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('phone', 20);
            $table->timestamp('whatsapp_verified_at')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->text('bio')->nullable();
            $table->enum('role', ['borrower', 'owner', 'admin'])->default('borrower');
            $table->boolean('is_owner_active')->default(false);
            $table->string('identity_doc', 255)->nullable();
            $table->enum('verification_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified');
            $table->text('verification_note')->nullable();
            $table->decimal('rating_avg_as_borrower', 3, 2)->default(0.00);
            $table->decimal('rating_avg_as_owner', 3, 2)->default(0.00);
            $table->enum('account_status', ['active', 'suspended', 'banned'])->default('active');
            $table->timestamp('suspended_until')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
