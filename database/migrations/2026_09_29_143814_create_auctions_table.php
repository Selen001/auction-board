<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();

            // Seller: the user who created the auction
            $table->foreignId('seller_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('item_name');
            $table->text('description');
            $table->decimal('base_price', 12, 2);
            $table->dateTime('closes_at');

            $table->enum('status', ['active', 'closed', 'cancelled'])
                ->default('active');

            $table->timestamps();

            // Listing open auctions and finding ones that need closing
            $table->index(['status', 'closes_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};