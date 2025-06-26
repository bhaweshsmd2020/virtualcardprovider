<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = array(
            array('id' => '1','title' => 'Basic','description' => 'Collect more submissions,
          access most of the features','price' => '10','is_featured' => '0','is_recommended' => '0','is_default' => '0','is_trial' => '0','status' => '1','days' => '30','trial_days' => '1','data' => '{"cards":{"value":10,"overview":"Card Limit: 10"},"deposit_fee":{"value":5,"overview":"Deposit Fee: 5%"},"transaction_fee":{"value":5,"overview":"Transaction Fee: 5%"},"service_fee":{"value":5,"overview":"Service Fee: 5%"}}','extra_data' => NULL,'created_at' => '2024-03-31 18:30:32','updated_at' => '2024-03-31 19:22:56'),
            array('id' => '2','title' => 'Enterprise','description' => 'Collect more submissions, 
          access most of the features','price' => '15','is_featured' => '1','is_recommended' => '0','is_default' => '0','is_trial' => '0','status' => '1','days' => '30','trial_days' => '15','data' => '{"cards":{"value":15,"overview":"Card Limit: 15"},"deposit_fee":{"value":4,"overview":"Deposit Fee: 4%"},"transaction_fee":{"value":4,"overview":"Transaction Fee: 4%"},"service_fee":{"value":4,"overview":"Service Fee: 4%"}}','extra_data' => NULL,'created_at' => '2024-03-31 18:30:32','updated_at' => '2024-03-31 19:42:30'),
            array('id' => '3','title' => 'Starter','description' => 'Collect more submissions, access most of the features','price' => '20','is_featured' => '0','is_recommended' => '0','is_default' => '0','is_trial' => '0','status' => '1','days' => '30','trial_days' => '0','data' => '{"cards":{"value":20,"overview":"Card Limit: 20"},"deposit_fee":{"value":8,"overview":"Deposit Fee: 2%"},"transaction_fee":{"value":2,"overview":"Transaction fee: 2"},"service_fee":{"value":2,"overview":"Service Fee: 2%"}}','extra_data' => NULL,'created_at' => '2024-03-31 18:30:32','updated_at' => '2024-03-31 19:45:10'),
            array('id' => '4','title' => 'Basic','description' => 'Collect more submissions,
          access most of the features','price' => '115','is_featured' => '0','is_recommended' => '0','is_default' => '0','is_trial' => '0','status' => '1','days' => '365','trial_days' => '1','data' => '{"cards":{"value":10,"overview":"Card Limit: 10"},"deposit_fee":{"value":5,"overview":"Deposit Fee: 5%"},"transaction_fee":{"value":5,"overview":"Transaction Fee: 5%"},"service_fee":{"value":5,"overview":"Service Fee: 5%"}}','extra_data' => NULL,'created_at' => '2024-03-31 18:30:32','updated_at' => '2024-03-31 19:47:19'),
            array('id' => '5','title' => 'Enterprise','description' => 'Collect more submissions, 
          access most of the features','price' => '170','is_featured' => '1','is_recommended' => '0','is_default' => '0','is_trial' => '0','status' => '1','days' => '365','trial_days' => '15','data' => '{"cards":{"value":15,"overview":"Card Limit: 15"},"deposit_fee":{"value":4,"overview":"Deposit Fee: 4%"},"transaction_fee":{"value":4,"overview":"Transaction Fee: 4%"},"service_fee":{"value":4,"overview":"Service Fee: 4%"}}','extra_data' => NULL,'created_at' => '2024-03-31 18:30:32','updated_at' => '2024-03-31 19:47:09'),
            array('id' => '6','title' => 'Starter','description' => 'Collect more submissions, access most of the features','price' => '220','is_featured' => '0','is_recommended' => '0','is_default' => '0','is_trial' => '0','status' => '1','days' => '365','trial_days' => '0','data' => '{"cards":{"value":20,"overview":"Card Limit: 20"},"deposit_fee":{"value":8,"overview":"Deposit Fee: 2%"},"transaction_fee":{"value":2,"overview":"Transaction fee: 2"},"service_fee":{"value":2,"overview":"Service Fee: 2%"}}','extra_data' => NULL,'created_at' => '2024-03-31 18:30:32','updated_at' => '2024-03-31 19:47:03'),
            array('id' => '7','title' => 'Forever','description' => 'Collect more submissions, access most of the features','price' => '500','is_featured' => '0','is_recommended' => '0','is_default' => '0','is_trial' => '0','status' => '1','days' => '999999','trial_days' => '0','data' => '{"cards":{"value":20,"overview":"Card Limit: 20"},"deposit_fee":{"value":2,"overview":"Deposit Fee: 2%"},"transaction_fee":{"value":2,"overview":"Transaction fee: 2"},"service_fee":{"value":2,"overview":"Service Fee: 2%"}}','extra_data' => NULL,'created_at' => '2024-03-31 18:30:32','updated_at' => '2024-03-31 19:49:20'),
            array('id' => '8','title' => 'Free','description' => 'Collect more submissions, access most of the features','price' => '0','is_featured' => '0','is_recommended' => '0','is_default' => '1','is_trial' => '0','status' => '1','days' => '999999','trial_days' => '0','data' => '{"cards":{"value":1,"overview":"Card Limit: 1"},"deposit_fee":{"value":10,"overview":"Deposit Fee: 10%"},"transaction_fee":{"value":10,"overview":"Transaction fee: 10"},"service_fee":{"value":10,"overview":"Service Fee: 10%"}}','extra_data' => NULL,'created_at' => '2024-03-31 18:30:32','updated_at' => '2024-03-31 19:49:20')
          );
          

        Plan::insert($plans);
    }
}
