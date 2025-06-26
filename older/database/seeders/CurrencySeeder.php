<?php

namespace Database\Seeders;

use App\Models\VirtualCurrency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $virtual_currencies = array(
            array('id' => '1','preview' => '/demo/tsA7ssiQNJq7v3J0QCwq.svg','currency' => 'usd','status' => 'active','precision' => '2','is_default' => '1','created_at' => '2024-03-31 21:28:07','updated_at' => '2024-04-02 14:09:20'),
            array('id' => '2','preview' => '/demo/FV7UWhQqDm1mbRKNMVXd.svg','currency' => 'euro','status' => 'active','precision' => '2','is_default' => '0','created_at' => '2024-03-31 21:52:09','updated_at' => '2024-04-02 14:10:22'),
            array('id' => '3','preview' => '/demo/KUVWqddbdo5PLFUN9yFJ.svg','currency' => 'usdt','status' => 'active','precision' => '2','is_default' => '0','created_at' => '2024-04-02 14:11:23','updated_at' => '2024-04-02 14:11:23')
        );
        
        VirtualCurrency::insert($virtual_currencies);
    }
}
