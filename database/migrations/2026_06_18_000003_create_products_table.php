<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title', 150);
            $table->string('slug', 180)->unique();
            $table->text('description');
            $table->enum('condition', ['new', 'like_new', 'good', 'fair']);
            $table->decimal('price_per_day', 12, 2);
            $table->decimal('deposit_amount', 12, 2)->default(0.00);
            $table->string('location_city', 100);
            $table->string('location_detail', 255)->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->decimal('rating_avg', 3, 2)->default(0.00);
            $table->integer('total_rented')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
