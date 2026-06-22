<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained('users');
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['borrower_id', 'owner_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
