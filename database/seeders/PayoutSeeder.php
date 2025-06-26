<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PayoutMethod;
class PayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payout_methods = array(
            array('id' => '1','currency_name' => 'USD','name' => 'Paypal','image' => '/uploads/24/03/HqAv7ClUIZ0ItMe8YPog.png','min_limit' => '20','max_limit' => '10000','delay' => '1','charge_type' => 'fixed','fixed_charge' => '5','percent_charge' => NULL,'data' => '[{"type": "text", "label": "Name"}, {"type": "email", "label": "Email"}]','instruction' => '<p>Enter the correct information properly</p>','rates' => '{"usd":"1","euro":"0.93"}','status' => '1','created_at' => '2024-03-31 22:01:01','updated_at' => '2024-03-31 22:01:01')
        );
          
        PayoutMethod::insert($payout_methods);
    }
}
