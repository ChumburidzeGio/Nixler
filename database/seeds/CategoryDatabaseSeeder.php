<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Entities\ProductCategory;

class CategoryDatabaseSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function sss($en, $pl = null, $ka = null, $icon = null, $sub = false)
    {
        $data = [
            'name:en' => $en,
            'name:pl' => $pl,
            'name:ka' => $ka,
            //'name:ru' => $ru,
            'icon' => $icon,
        ];

        if($sub){
            $data['subcategories'] = $sub;
        }

        return $data;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $categories = [
            $this->sss('Fashion', 'Moda', 'მოდა', 'wc', [
                $this->sss('Clothing', 'Ubrania', 'ტანსაცმელი'),
                $this->sss('Shoes', 'Buty', 'ფეხსაცმელი'),
                $this->sss('Accessories', 'Akcesoria', 'აქსესუარები'),
                $this->sss('Jewelry', 'Biżuteria', 'სამკაულები'),
                $this->sss('Watches', 'Zegarki', 'საათები'),
                $this->sss('Handbags & Wallets', 'Torebki i portfele', 'ხელჩანთები და საფულეები'),
                $this->sss('Bags', 'Torby', 'ჩანთები'),
            ]),
            $this->sss('Electronics', 'Elektronika', 'ელექტრონიკა', 'phonelink', [
                $this->sss('Mobile Phones'),
                $this->sss('Phone accessories'),
                $this->sss('Photo & Video'),
                $this->sss('Headphones'),
                $this->sss('Video Games'),
                $this->sss('Wireless Speakers'),
                $this->sss('Car Electronics'),
                $this->sss('Musical Instruments'),
                $this->sss('Wearable Technology'),
                $this->sss('Computers'),
                $this->sss('Tablets'),
                $this->sss('Monitors'),
                $this->sss('Networking'),
                $this->sss('Computer Parts'),
                $this->sss('Software'),
                $this->sss('Printers & Ink'),
                $this->sss('TV'),
                $this->sss('Notebooks'),
                $this->sss('Drives & Storage'),
                $this->sss('Smart Home'),
            ]),
            $this->sss('Home & Furniture', 'Dom i Meble', 'სახლი და ავეჯი', 'weekend', [
                $this->sss('Furniture'),
                $this->sss('Kitchen & Dining'),
                $this->sss('Bath & Badding'),
                $this->sss('Home Decor'),
                $this->sss('Garden & Outdoor'),
                $this->sss('Lightning'),
                $this->sss('Pet Supplies'),
                $this->sss('Home improvement'),
                $this->sss('Home electronics'),
            ]),
            $this->sss('Sports & Outdoors', null, null, 'directions_bike', [
                $this->sss('Sports Clothing'),
                $this->sss('Fitness & Body Building'),
                $this->sss('Hunting & Fishing'),
                $this->sss('Team Sports'),
                $this->sss('Fan Shop'),
                $this->sss('Camping & Hiking'),
                $this->sss('Cycling'),
                $this->sss('Scooters, Skateboards & Skates'),
                $this->sss('Sports Bags'),
                $this->sss('Water Sports'),
                $this->sss('Winter Sports'),
                $this->sss('Climbing'),
                $this->sss('Sport Accessories'),
            ]),
            $this->sss('Health & Beauty', null, null, 'local_florist', [
                $this->sss('Makeup'),
                $this->sss('Skin Care'),
                $this->sss('Hair Care'),
                $this->sss('Fragrance'),
                $this->sss('Tools & Accessories'),
                $this->sss('Shave & Hair Removal'),
                $this->sss('Personal Care'),
                $this->sss('Oral Care'),
                $this->sss('Vitamins'),
                $this->sss('Minerals'),
                $this->sss('Supplements'),
                $this->sss('Herbal Supplements'),
                $this->sss('Weight Loss'),
                $this->sss('Sports Nutrition'),
            ]),
            $this->sss('Kids & Baby', null, null, 'child_friendly', [
                $this->sss('Toys & Games'),
                $this->sss('Moms & Baby'),
                $this->sss('Diapers'),
                $this->sss('Baby food'),
                $this->sss('Carriages'),
                $this->sss('Bedspreads'),
                $this->sss('Baby car seats'),
                $this->sss('Hobby & Creativity'),
                $this->sss('Children sport'),
                $this->sss('For school'),
            ]),
        ];

        foreach ($categories as $key => $value) {
            $value['order'] = $key;
            $subcategories = isset($value['subcategories']) ? $value['subcategories'] : [];
            unset($value['subcategories']);
            $category = ProductCategory::create(array_filter($value, 'strlen'));

            foreach ($subcategories as $key => $value) {
                $value['order'] = $key;
                $category->children()->create(array_filter($value, 'strlen'));
            }
        }
    }

}
