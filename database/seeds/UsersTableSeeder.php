<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory;
use Faker\Generator;
use App\Entities\User;
use App\Repositories\ShippingRepository;

class UsersTableSeeder extends Seeder
{

    protected $shippingRepository;

    public function __construct(ShippingRepository $repository){
        $this->shippingRepository = $repository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //foreach (config('test.locales') as $locale) {
           
            $faker = Factory::create('en_US');

            for ($i=0; $i < 30; $i++) { 
                $user = $this->createUser($faker);
                $this->createRelationships($user);
                $this->uploadPhotos($user, $faker);
                print("\nCreated ".$user->name);
            }
        //}
    }


    /**
     * Create user
     *
     * @return void
     */
    public function getCountry()
    {
        return collect([[
            'country' => 'US',
            'timezone' => 'America/New_York',
            'currency' => 'USD',
            'locale' => 'en',
        ],[
            'country' => 'PL',
            'timezone' => 'Europe/Warsaw',
            'currency' => 'PLN',
            'locale' => 'pl',
        ],[
            'country' => 'GE',
            'timezone' => 'Asia/Tbilisi',
            'currency' => 'GEL',
            'locale' => 'ka',
        ]])->random();
    }


    /**
     * Create user
     *
     * @return void
     */
    public function createUser($faker)
    {
        $user = User::create([
            'name' => $faker->name,
            'email' => $faker->email,
            'password' => bcrypt('test')
        ]);

        $country = $this->getCountry();

        $user->country = array_get($country, 'country');
        $user->timezone = array_get($country, 'timezone');
        $user->currency = array_get($country, 'currency');
        $user->locale = array_get($country, 'locale');
        $user->save();

        $this->shippingRepository->settingsUpdate([
            'delivery_full' => 1,
            'has_return' => 1,
            'policy' => ''
        ], $user);

        return $user;
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
