<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()->setLocale(config('app.fallback_locale'));
        $this->call(UsersTableSeeder::class);
        $this->call(ProductDatabaseSeeder::class);
    }
}
