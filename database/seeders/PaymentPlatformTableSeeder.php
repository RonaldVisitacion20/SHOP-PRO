<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentPlatform;
class PaymentPlatformTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentPlatform::create([
            'name'=>'Paypal',
            'image'=>'img/payment-platform/paypal.jpg'

        ]);
        PaymentPlatform::create([
            'name'=>'Stripe',
            'image'=>'img/payment-platform/stripe.jpg'

        ]);
    }
}
