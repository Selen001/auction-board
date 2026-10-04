<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->foreignId('winner_id')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('winning_bid_id')
                ->nullable()
                ->after('winner_id')
                ->constrained('bids')
                ->nullOnDelete();

            $table->decimal('winning_amount', 12, 2)->nullable()->after('winning_bid_id');
            $table->timestamp('closed_at')->nullable()->after('winning_amount');
        });
    }

    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('winner_id');
            $table->dropConstrainedForeignId('winning_bid_id');
            $table->dropColumn(['winning_amount', 'closed_at']);
        });
    }
};