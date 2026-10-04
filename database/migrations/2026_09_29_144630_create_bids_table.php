<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();

            $table->foreignId('auction_id')
                ->constrained('auctions')
                ->cascadeOnDelete();

            // Buyer: the user who placed the bid
            $table->foreignId('buyer_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('amount', 12, 2);

            // created_at is the bid timestamp
            $table->timestamps();

            // Fast "highest bid for this auction" lookups and bid history
            $table->index(['auction_id', 'amount']);
            $table->index(['auction_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};