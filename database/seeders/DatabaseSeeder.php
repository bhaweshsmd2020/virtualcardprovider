<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\OptionsTableSeeder;
use Database\Seeders\GatewaysTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            CurrencySeeder::class,
            CategoriesTableSeeder::class,
            GatewaysTableSeeder::class,
            MenusTableSeeder::class,
            OptionsTableSeeder::class,
            PlansTableSeeder::class,
            PostsTableSeeder::class,
            PostCategoryTableSeeder::class,
            PostMetaTableSeeder::class,
            UsersTableSeeder::class,
            CardTableSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            ExchangeratesSeeder::class,
            PayoutSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
