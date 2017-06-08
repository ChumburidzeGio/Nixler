<?php

namespace App\Upgrade;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Entities\Product;
use App\Repositories\ProductRepository;
use App\Repositories\LocationRepository;
use App\Entities\Region;

class OnePointNinetyOne
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function upgrade()
    {
    	if (!Schema::hasColumn('users', 'notifications_count')) {

            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('notifications_count')->default(0);
            });

        }

        Schema::table('products', function (Blueprint $table) {

            if (!Schema::hasColumn('products', 'media_id')) {
                $table->integer('media_id')->unsigned()->nullable();
            }

            if (Schema::hasColumn('products', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('products', 'is_used')) {
                $table->dropColumn('is_used');
            }

            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(0);
            }

        });


        $products = Product::all();

        $products->map(function($product) {

            if(!$product->is_active) {
                $product->is_active = true;
                $product->save();
            }

            if(!$product->media_id){
                app(ProductRepository::class)->refreshFeaturedMediaForProduct($product);
            }

        });

        if (Schema::hasColumn('products', 'status')) {
          
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['status']);
            });

        }

        $geonamesIds = [
            '610864' => '865539', //Zestafoni
            '612287' => '865536', //Rustavi
            '612366' => '865543', //Poti
            '614455' => '865540', //Gori
            '611403' => '865540', //Tskhinvali
            '612126' => '865539', //Samtredia
            '611694' => '865537', //Telavi
            '612338' => '865537', //Kvareli
            '614351' => '865537', //Gurjaani
            '612230' => '865537', //Sagarejo
            '615844' => '865537', //Akhmeta
            '612890' => '865541', //Mtskheta
            '613988' => '865540', //Khashuri
            '612053' => '865543', //Senaki
        ];

        foreach ($geonamesIds as $geonamesId => $regionGeonamesId) {

            $region = Region::where('geonames_id', $regionGeonamesId)->first();

            if($region){
                app(LocationRepository::class)->donwloadCity($geonamesId, 'ka', 1, $region->id);
            }

        }

    }

}