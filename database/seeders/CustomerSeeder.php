<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerSeeder extends Seeder
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
            // Use batch processing
            $batchSize = 100; // Adjust this based on your needs
            $totalRecords = 1000;

            for ($i = 0; $i < $totalRecords; $i += $batchSize) {
                Customer::factory($batchSize)
                    ->has(BillingAddress::factory()->count(2))
                    ->has(ShippingAddress::factory()->count(rand(1, 2)))
                    ->create();
                    
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
