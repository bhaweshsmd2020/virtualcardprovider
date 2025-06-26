<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;
class ExchangeratesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exchange_rates = array(
            array('id' => '1','virtual_currency_id' => '2','virtual_currency_exchange_id' => '1','rate' => '1.08','created_at' => '2024-03-31 21:52:09','updated_at' => '2024-03-31 22:03:31'),
            array('id' => '2','virtual_currency_id' => '1','virtual_currency_exchange_id' => '2','rate' => '1.08','created_at' => '2024-03-31 21:52:55','updated_at' => '2024-03-31 21:53:24'),
            array('id' => '3','virtual_currency_id' => '3','virtual_currency_exchange_id' => '1','rate' => '1','created_at' => '2024-04-03 08:09:09','updated_at' => '2024-04-03 08:09:09'),
            array('id' => '4','virtual_currency_id' => '3','virtual_currency_exchange_id' => '2','rate' => '1','created_at' => '2024-04-03 08:09:09','updated_at' => '2024-04-03 08:09:09'),
            array('id' => '5','virtual_currency_id' => '2','virtual_currency_exchange_id' => '3','rate' => '1','created_at' => '2024-04-03 08:09:13','updated_at' => '2024-04-03 08:09:13'),
            array('id' => '6','virtual_currency_id' => '1','virtual_currency_exchange_id' => '3','rate' => '1','created_at' => '2024-04-03 08:09:21','updated_at' => '2024-04-03 08:09:21')
          );
          
        
        ExchangeRate::insert($exchange_rates); 
    }
}
