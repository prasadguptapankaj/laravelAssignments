<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
use App\Models\BillingAddress;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\Payment;
use App\Models\Customer;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first();
        $billingAddress = $customer->billingAddresses->random();
        $shippingAddress = $customer->shippingAddresses->random();
        $status = $this->faker->randomElement(['new', 'processed', 'unprocessed']);
        
        return [
            'customer_id' => $customer->id,
            'billing_address_id' => $billingAddress->id,
            'shipping_address_id' => $shippingAddress->id,
            'status' => $status,
            'total_amount' => $this->faker->randomFloat(2, 50, 1000),
            'created_at' => $this->faker->dateTimeBetween('2018-01-01', '2023-12-31'),
            'updated_at' => now(),
        ];
    }

    
}
