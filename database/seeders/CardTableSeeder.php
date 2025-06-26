<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Card;
class CardTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = array(
            array('id' => '1','uuid' => '855e6e9b-46e1-4156-9826-8357246ff2cd','category_id' => '95','title' => 'Ultima','min_deposit' => '100','required_balance' => '500','description' => 'Convenient virtual card for online ads (including Bing, Twitter, Pinterest, and more) and online purchases.','preview' => '/uploads/24/02/UaLM9lJEGXYVz0yk0PBd.png','type' => NULL,'status' => 'active','card_variant' => 'gold','category_type' => NULL,'categories' => NULL,'is_featured' => '1','created_at' => '2024-02-29 17:51:40','updated_at' => '2024-02-29 17:51:40'),
            array('id' => '2','uuid' => '4cf36460-6f66-4f8d-92ed-4935ea78d539','category_id' => '96','title' => 'Advertisement','min_deposit' => '50','required_balance' => '60','description' => 'Universal virtual card for all advertising platforms, including Google ads. It’s the best match for solopreneurs, freelance marketers, and small businesses.','preview' => '/uploads/24/02/CbDbAgYx9y8ukUdmrpYD.png','type' => NULL,'status' => 'active','card_variant' => 'silver','category_type' => 'allowed','categories' => '["advertising_services"]','is_featured' => '0','created_at' => '2024-02-29 17:52:53','updated_at' => '2024-02-29 17:52:53'),
            array('id' => '3','uuid' => '540e4a0a-842c-470b-a061-72cb12d50d87','category_id' => '97','title' => 'Facebook','min_deposit' => '30','required_balance' => '50','description' => 'Virtual payment card designed for Facebook Ads. No more risk payment bans or other payment problems, thanks to the decline rate and success rate monitoring system from E-CARD.','preview' => '/uploads/24/02/JOT64lbRui8fwMAr1Oru.png','type' => NULL,'status' => 'active','card_variant' => 'pro','category_type' => 'allowed','categories' => '["advertising_services"]','is_featured' => '0','created_at' => '2024-02-29 17:53:55','updated_at' => '2024-02-29 17:57:10'),
            array('id' => '4','uuid' => '97cbfc48-eda4-46e0-80f7-a812f15c726d','category_id' => '98','title' => 'Google','min_deposit' => '50','required_balance' => '80','description' => 'Virtual payment card for Google Ads spend.  And no more risk payment bans or other payment issues.','preview' => '/uploads/24/02/g0LO4RUmDncm1c1ynJwO.png','type' => NULL,'status' => 'active','card_variant' => 'basic','category_type' => 'allowed','categories' => '["advertising_services"]','is_featured' => '0','created_at' => '2024-02-29 17:55:28','updated_at' => '2024-02-29 17:55:28'),
            array('id' => '5','uuid' => 'cf297997-7551-49d6-bc6d-d51b6aed2f04','category_id' => '99','title' => 'TikTok','min_deposit' => '50','required_balance' => '100','description' => 'Virtual card with two new designed for TikTok Ads. Auto 3Ds-support,  and elevated trust level for your ads’ smooth sailing.','preview' => '/uploads/24/02/XhPPGpUDcrH6RfrmjnLJ.png','type' => NULL,'status' => 'active','card_variant' => 'basic','category_type' => 'allowed','categories' => '["advertising_services"]','is_featured' => '0','created_at' => '2024-02-29 17:56:34','updated_at' => '2024-02-29 17:56:34')
          );
        
        Card::insert($cards);  
          
    }
}
