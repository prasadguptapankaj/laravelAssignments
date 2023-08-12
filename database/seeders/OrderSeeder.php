<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            foreach (array_chunk(range(1, 50000), 1000) as $chunkIndex => $chunk) {
                DB::transaction(function () use ($chunk) {
                    Order::factory()
                        ->count(count($chunk))
                        ->has(OrderItem::factory()->count(5))
                        ->has(Payment::factory())
                        ->create();
                });
            };
        } catch (\Exception $e) {
            throw $e;
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
}
