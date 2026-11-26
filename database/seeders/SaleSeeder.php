<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Genera ventas de demostración recientes y descuenta las unidades vendidas.
 */
class SaleSeeder extends Seeder
{
    private const TOTAL_SALES = 12;

    public function run(): void
    {
        $customers = Customer::all();
        $users = User::all();

        for ($i = 0; $i < self::TOTAL_SALES; $i++) {
            $available = Medicine::where('amount', '>', 0)->get();

            if ($available->isEmpty()) {
                break;
            }

            $sale = Sale::create([
                'customer_id' => $customers->random()->id,
                'users_id' => $users->random()->id,
                'date' => now()->subDays(fake()->numberBetween(0, 14))
                    ->setTime(fake()->numberBetween(8, 21), fake()->numberBetween(0, 59)),
                'total' => 0,
                'igv' => 0,
            ]);

            $total = 0;

            foreach ($available->random(min(fake()->numberBetween(1, 3), $available->count())) as $medicine) {
                $amount = min(fake()->numberBetween(1, 3), $medicine->amount);
                $subtotal = $medicine->price_unit * $amount;

                $sale->details()->create([
                    'medicines_id' => $medicine->id,
                    'amount' => $amount,
                    'price_unit' => $medicine->price_unit,
                    'subtotal' => $subtotal,
                ]);

                $medicine->decrement('amount', $amount);
                $total += $subtotal;
            }

            $sale->update([
                'total' => $total,
                'igv' => round($total * 18 / 118, 2),
            ]);
        }
    }
}
