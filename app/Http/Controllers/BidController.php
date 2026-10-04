<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BidController extends Controller
{
    public function store(Request $request, Auction $auction): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999.99'],
        ]);

        $user = $request->user();
        $fail = fn (string $message) => throw ValidationException::withMessages(['amount' => $message]);

        DB::transaction(function () use ($auction, $user, $data, $fail) {
            // Lock the auction row so simultaneous bids and the closer run one at a time
            $locked = Auction::whereKey($auction->id)->lockForUpdate()->firstOrFail();

            // Closing time is checked against the server clock
            if (! $locked->isOpen()) {
                $fail('This auction is closed and no longer accepts bids.');
            }

            if ((int) $locked->seller_id === (int) $user->id) {
                $fail('You cannot bid on your own auction.');
            }

            $top = $locked->bids()->orderByDesc('amount')->orderBy('id')->first();
            $cents = (int) round($data['amount'] * 100);

            if ($top === null) {
                if ($cents < (int) round($locked->base_price * 100)) {
                    $fail('Your bid must be at least the base price of $' . number_format($locked->base_price, 2) . '.');
                }
            } else {
                if ((int) $top->buyer_id === (int) $user->id) {
                    $fail('You are already the highest bidder.');
                }
                if ($cents <= (int) round($top->amount * 100)) {
                    $fail('Your bid must be higher than the current highest bid of $' . number_format($top->amount, 2) . '.');
                }
            }

            $locked->bids()->create([
                'buyer_id' => $user->id,
                'amount' => $data['amount'],
            ]);
        });

        return redirect()
            ->route('auctions.show', $auction)
            ->with('status', 'Your bid of $' . number_format($data['amount'], 2) . ' was placed.');
    }
}