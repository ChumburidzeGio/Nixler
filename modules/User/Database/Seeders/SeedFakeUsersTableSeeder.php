<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory;
use Faker\Generator;
use Modules\User\Entities\User;

class SeedFakeUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('test.locales') as $locale) {
           
            $faker = Factory::create($locale);

            for ($i=0; $i < config('test.models_per_locale.users'); $i++) { 
                $user = $this->createUser($faker);
                $this->createRelationships($user);
                $this->createEmails($user, $faker);
                $this->uploadPhotos($user, $faker);
            }
        }
    }


    /**
     * Create user
     *
     * @return void
     */
    public function createUser($faker)
    {
        return User::create([
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => bcrypt('test')
        ]);
    }


    /**
     * Follow users
     *
     * @return void
     */
    public function createRelationships($user)
    {
        $user_ids = User::inRandomOrder()->where('id', '<>', $user->id)->take(rand(10,30))->pluck('id')->toArray();
        $user->follow($user_ids);
    }


    /**
     * Create emails
     *
     * @return void
     */
    public function createEmails($user, $faker)
    {
        for ($i=0; $i < rand(2,8); $i++) {
            $email = $user->emails()->create([
                'address' => $faker->email
            ]);

            $code = $email->verify();
            $email->makeVerified($code);
            $email->makeDefault();
        }
    }


    /**
     * Upload photos
     *
     * @return void
     */
    public function uploadPhotos($user, $faker)
    {
        if($faker->boolean()){
            $user->uploadPhoto($faker->image('/tmp', 400, 400), 'avatar');
        }

        if($faker->boolean(10)){
            $user->uploadPhoto($faker->image('/tmp', 1200, 400), 'cover');
        }
    }
}
