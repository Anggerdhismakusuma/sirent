<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_request_id')->constrained('rental_requests');
            $table->foreignId('rater_id')->constrained('users');
            $table->foreignId('ratee_id')->constrained('users');
            $table->enum('type', ['to_owner', 'to_borrower']);
            $table->tinyInteger('score');
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['rental_request_id', 'rater_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
