<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderItem;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{

    public function run(): void
    {

        // Schema::disableForeignKeyConstraints();

        try {
            foreach (array_chunk(range(1, 50000), 1000) as $chunkIndex => $chunk) {
                DB::transaction(function () use ($chunk) {
                    Order::factory()
                        ->count(count($chunk))
                        ->afterCreating(function ($order) {
                            $totalPrice = $order->orderItems->sum(function ($orderItem) {
                                return $orderItem->price * $orderItem->quantity;
                            });
                    
                            $order->total_amount = $totalPrice;
                    
                            $order->save();
                        })
                        ->has(OrderItem::factory()->count(5))
                        ->has(Payment::factory())
                        ->create();
                });
            };
        } catch (\Exception $e) {
            throw $e;
        } finally {
            // Schema::enableForeignKeyConstraints();
        }
    }
}
