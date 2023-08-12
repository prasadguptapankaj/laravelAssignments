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
        // Disable foreign key constraints
        Schema::disableForeignKeyConstraints();

        // Start a database transaction
        DB::beginTransaction();

        try {
            $totalOrders = 50000;
            $ordersPerBatch = 100; // Adjust this based on your needs
            $itemsPerOrder = 5;

            for ($i = 0; $i < $totalOrders; $i += $ordersPerBatch) {
                $orders = Order::factory($ordersPerBatch)->create();

                foreach ($orders as $order) {
                    $orderItems = [];
                    for ($j = 0; $j < $itemsPerOrder; $j++) {
                        $orderItems[] = OrderItem::factory()->make();
                    }

                    
                    $order->orderItems()->saveMany($orderItems);
                    $order->payment()->save(Payment::factory()->make());
                }
            }

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollback();
            throw $e;
        } finally {
            // Re-enable foreign key constraints
            Schema::enableForeignKeyConstraints();
        }
    }
}
