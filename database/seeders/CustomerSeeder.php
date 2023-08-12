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

        foreach (array_chunk(range(1, 1000), 100) as $chunkIndex => $chunk) {
            DB::transaction(function () use ($chunk) {
                Customer::factory()
                    ->count(count($chunk))
                    ->has(BillingAddress::factory()->count(2))
                    ->has(ShippingAddress::factory()->count(1))
                    ->create();
            });
        }
    }
}
