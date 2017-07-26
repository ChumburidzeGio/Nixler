<?php

namespace App\Crawler\Patterns;

use Goutte\Client;
use App\Crawler\BasePattern;
use Symfony\Component\DomCrawler\Crawler;
use App\Crawler\Crawler as LC;
use Symfony\Component\DomCrawler\Link;

class Zalando extends BasePattern {

    protected $data;

    protected $lcen;

    /**
     * Construct the pattern
     *
     * @param $crawler Crawler
     * 
     * @return $this
     */
    public function parse(Crawler $crawler)
    {
        parent::parse($crawler);

        if($this->isProduct()) {

            $json = $this->crawler('#z-vegas-pdp-props')->first()->text();

            $json = ltrim($json, '<![CDATA[');

            $json = rtrim($json, ']>');

            $this->data = json_decode($json, 1);

            if(!$this->isUK()) {

                $enUrl = 'https://www.zalando.co.uk/catalog/?qf=1&q=';

                $this->lcen = app(LC::class)->get($enUrl.array_get($this->data, 'model.articleInfo.id'));

            }
            
        }

        return $this;
    }

    /**
     * Check if its Zalando.co.uk
     *
     * @return bool
     */
    public function isUK() : bool
    {
        return !!($this->crawler('html')->first()->attr('lang') == 'en-GB');
    }

    /**
     * Check if we are on product page
     *
     * @return boolean
     */
    public function isProduct()
    {
        return !!$this->crawler('#z-vegas-pdp-props')->count();
    }

    /**
     * Get the list of products from page
     *
     * @return boolean
     */
    public function detectProductsOnPage()
    {
        $products = $this->crawler('#catalogItemsListParent .catalogArticlesList_item');

        if(!$products->count()){
            return [];
        }

        return array_unique($products->each(function($a){
            return $a->filter('a')->link()->getUri();
        }));
    }

    /**
     * Parse the title for product
     *
     * @return string
     */
    public function getTitle()
    {
        if(!$this->isUK() && $this->lcen) {

            return $this->lcen->getTitle();

        }

        $name = array_get($this->data, 'model.articleInfo.name');

        $brand = array_get($this->data, 'model.articleInfo.brand.name');

        return "{$name} - {$brand}";
    }

    /**
     * Parse the description for product
     *
     * @return string
     */
    public function getDescription()
    {
        //return array_get($this->data, 'model.articleInfo');

        if(!$this->isUK() && $this->lcen) {

            return $this->lcen->getDescription();

        }
        
        $description = collect(array_get($this->data, 'model.articleInfo.attributes'))->map(function($item) {
            
            $header = $this->translate(array_get($item, 'category'));

            $data = [];

            foreach (array_get($item, 'data') as $key => $item) {
                
                if(array_get($item, 'name') == 'config_sku' || !array_get($item, 'name') || !array_get($item, 'values')) {
                    continue;
                }

                $data[] = $this->translate(array_get($item, 'name')).': '.$this->translate(array_get($item, 'values'));

            }

            $data = implode("\n", $data);

            return "#{$header}\n{$data}";

        });

        return implode("\n\n", $description->toArray());
    }

    /**
     * Parse the image of product
     *
     * @return array
     */
    public function getMedia()
    {
        return collect(array_get($this->data, 'model.articleInfo.media.images'))->map(function($item) {

            return array_get($item, 'sources.zoom');

        });
    }

    /**
     * Parse the variants of product
     *
     * @return array
     */
    public function getVariants()
    {
        return collect(array_get($this->data, 'model.articleInfo.units'))->map(function($item) {

            $price = array_get($item, 'displayPrice.originalPrice.value');

            $currency = array_get($item, 'displayPrice.originalPrice.currency');

            return [
                'price' => $this->calcPrice($price, $currency),
                'in_stock' => array_get($item, 'stock'),
                'name' => $this->transfromSize(array_get($item, 'size.local'), array_get($item, 'size.local_type'))
            ];

        });
    }

    /**
     * Transform size to EU one
     *
     * @return int
     */
    public function transfromSize(string $size, string $type) : string
    {
        if(is_numeric($size)){

            if($type == 'UK')  {

                $size = $size + 28;

            }

            elseif($type == 'US')  {

                $size = ($size == 1) ? 32 : $size + 32;

            }

            $size = $size.' ზომა';

        }

        if($size == 'One Size') {
            return 'ერთი ზომა';
        }

        return $size;
    }

    /**
     * Translate word
     *
     * @return string
     */
    public function translate(string $word) : string
    {
        $word = array_get([
            'heading_material' => 'ქსოვილი',
            'heading_details' => 'დეტალები',
            'heading_measure_and_fitting' => 'ზომა',
            'Zip' => 'ელვა',
            'Floral' => 'ყვავილოვანი',
            'Long' => 'გრძელი',
            'Cropped' => 'გადაჭრილი',
            'Round neck' => 'მომრგვალებული საყელო',
            'Sleeveless' => 'უსახელო',
            'Calf-length' => 'მუხლს ქვემოთ',
            'Low V-neck' => 'დაბალი V-ტიპის ჭრილი',

            'pink' => 'ვარდისფერი',
            'purple' => 'იასამნისფერი',
            'multicoloured' => 'ფერადი',
            'white' => 'თეთრი',
            'black' => 'შავი',
            'dark blue' => 'მუქი ლურჯი',
            'blue' => 'ცისფერი',

            'Trouser' => 'შარვალი',
            'Sports shoes' => 'სპორტული ფეხსაცმელი',
            'Scarf' => 'შარფი',
            'Dress' => 'კაბა',
            'Summer dress' => 'საზაფხულო კაბა',
            'Shirt' => 'პერანგი',
            'Shorts' => 'შორტები',
            'Ballerina Shoe' => 'ბალეტკები',
        ], $word, $word);

        $word = strtr($word, [
            'Outer fabric material' => 'შემადგენლობა',
            'Washing instructions' => 'რეცხვა',
            'Neckline' => 'საყელო',
            'Sheer' => 'გამჭვირვალე',
            'Semi-sheer' => 'ნახევრად-გამჭვირვალე',
            'Pattern' => 'მოხატულობა',
            'Details' => 'დეტალები',
            'Our model\'s height' => 'მოდელის სიმაღლე',
            'Length' => 'სიგრძე',
            'Sleeve length' => 'სახელოების სიგრძე',
            'Back width' => 'ზურგის სიფართე',
            'Lunghezza delle maniche' => 'სახელოების სიგრძე',
            'Total length' => 'სრული სიგრძე',
            'Flat' => 'ბრტყელი',
            'Short' => 'მოკლე',
            'cotton' => 'ბამბა',
            'polyester' => 'პოლიესტერინი',
            'Do not tumble dry' => 'არ გააშროთ სარეცხ-მანქანაში',
            'machine wash at' => 'რეცხვის ტემპერატურა',
            'Our model is' => 'ჩვენი მოდელის სიმაღლე არის',
            'tall and is wearing size' => 'და იცვავს ზომას',
            'Size' => 'ზომა',
            'Normal' => 'ნორმალური',
            'Regular' => 'ჩვეულებრივი',
            'Print' => 'პრინტი',
            'Machine wash at' => 'სარეცხ-მანქანაში რეცხვა ტემპერატურაზე',
            'Machine wash on gentle cycle' => 'რეცხვა დელიკატურზე',
            'Fastening' => 'შესაკრავი',
            'Button' => 'ღილი',
            'Plain' => 'გლუვი',
            'Zip fastening' => 'ელვა შესაკრავი',
            'Knee-length' => 'მუხლამდე სიგრძე',
            'Hand wash only' => 'მხოლოდ ხელით რეცხვა',
        ]);

        $word = preg_replace_callback("/(\d.+)\"/", function($matches){
            return round(floatval($matches[1]) / 0.393700787). ' სმ';
        }, $word);

        $word = preg_replace_callback("/((ზომა|ზომას) (\d+))/", function($matches){
            return array_get($matches, 2) . ' ' . $this->transfromSize(array_get($matches, 3), 'UK');
        }, $word);

        return $word;
    }

    /**
     * Parse the category of product
     *
     * @return integer
     */
    public function getCategory()
    {
        if(!$this->isUK() && $this->lcen) {

            return $this->lcen->getCategory();

        }

        $category = array_get($this->data, 'model.articleInfo.category_tag');

        $cat2l = strtolower($category);

        if(str_contains($cat2l, [
            'shoe', 'boot', 'trainer', 'heel', 'sandal', 'slip-on'
        ])) {
            return 3;
        }

        if(str_contains($cat2l, [
            'handbag', 'tote bag', 'rucksack', 'body bag'
        ])) {
            return 8;
        }

        if(str_contains($cat2l, [
            'bracciale', 'bracelet'
        ])) {
            return 5;
        }

        if(str_contains($cat2l, [
            'wallet'
        ])) {
            return 7;
        }

        if(str_contains($cat2l, [
            'dress', 'shirt', 'jacket', 'cape', 'trouser', 'skirt', 'shorts', 'bikini', 'cardigan', 'suit', 'vest', 'bras', 
            'briefs', 'bustier', 'pyjama', 'coat', 'blazer', 'sock', 'sleeved', 'jumper'
        ])) {
            return 2;
        }

        return $this->findCategory($category, [
            3 => [
                'Peep Toes', 'Wedges', 'Flats & Lace-Ups', 'Brogues & Lace-Ups', 'Espadrilles', 'Loafers', 'Moccasins', 'Sporty Lace-Ups', 
                'Wellies', 'Mules & Clogs', 'Clogs', 'Mules', 
                'Ballet Pumps', 'Ankle Cuff Ballet Pumps', 'Ankle Strap Ballet Pumps', 'Classic Ballet Pumps', 
                'Foldable Ballet Pumps', 'Peep-Toe Ballet Pumps', 'Sling-back Ballet Pumps', 
                'Flip Flops & Beach Shoes', 'Beach Shoes & Jelly Shoes', 'Flip Flops', 
                'Sports Shoes', 'Cushioned', 'Stability', 'Lightweight', 'Natural Running', 'Trail', 'Walking', 
                'Football Boots', 'Moulded Soles', 
                'Basketball Shoes', 'Tennis Shoes', 'Clay Court Shoes', 'Indoor Court Shoes', 'Golf shoes', 
                'Trainers & Fitness Shoes', 'Trainers', 'Indoor Shoes', 'Dance & Ballet Shoes', 'Beach Shoes', 
                'Watersports Shoes', 'Hiking & Hillwalking Shoes', 'Hiking Boots', 'Walking Boots', 'Mountain Boots', 
                'Trail Shoes', 'Trekking Boots', 'Boots', 'Winter Boots', 'Wellies', 'Ski & Snowboard Boots', 
                'Low-tops', 'High-tops', 'Trainers', 'Cycling Shoes', 
                'Slippers', 'Shoe Care', 'Shoe Trees', 'Soles and Insoles', 'Lace-up Boots', 'Casual Shoes', 
                'Brogues', 'Slip Ons', 'Boat Shoes', 'Sporty Lace Ups', 'Espadrilles', 'Formal Shoes', 'Derbies & Oxfords', 
                'Loafers', 'Sliders', 'Slides & Clogs', 
            ],
            2 => [
                'Long Sleeve Tops',
                'Jeans', 'Skinny Fit', 'Flares', 'Bootcut', 'Denim Shorts', 'Loose Fit', 'Slim Fit', 'Straight Leg', 
                'Hoodies', 'Jumpers', 'Fleece Jumpers', 
                'Chinos', 'Joggers & Sweats', 'Leggings', 'Shorts', 
                'Blouses & Tunics', 'Blouse', 'Tunics', 
                'Lingerie & Nightwear', 'Knickers', 'Nightwear', 'Suspenders', 
                'Shapewear', 'Push-ups',
                'Panties & French Knickers', 'Strings & Thongs', 'Nighties/Slips', 'Sets', 
                'Bodies', 'Corsets', 'Leggings', 'Leg Warmers', 'Suspenders', 
                'Tights', 'Swimwear', 'Bathrobes', 'Beach Accessories', 
            ]
        ]);

    }

    /**
     * Parse the tags of product
     *
     * @return array
     */
    public function getTags() : array
    {
        if(!$this->isUK() && $this->lcen) {

            return $this->lcen->getTags();

        }

        $color = $this->translate(array_get($this->data, 'model.articleInfo.color'));

        $category = $this->translate(array_get($this->data, 'model.articleInfo.category_tag'));

        $silhouetteCode = $this->translate(
            str_replace('_', ' ', title_case(array_get($this->data, 'model.articleInfo.silhouette_code')))
        );

        return array_values(compact('color', 'category', 'silhouetteCode'));
    }

    /**
     * Calculate price
     *
     * @return int
     */
    public function calcPrice(float $price, string $currency) : int
    {
        $EUR2GEL = 2.80;

        $GBP2GEL = 3.15;

        $FEE = 5;

        if($currency == 'GBP') {

            $price = money(null, ($price + 5) * $GBP2GEL);

        } elseif ($currency == 'EUR') {

            $price = money(null, ($price + 4) * $EUR2GEL);

        }

        $price = str_replace(',', '', $price);

        return round($price + $FEE);
    }

    /**
     * Get target group
     *
     * @return string
     */
    public function getTarget()
    {
        $age = array_get($this->data, 'model.articleInfo.targetGroups.age');

        $gender = array_get($this->data, 'model.articleInfo.targetGroups.gender');

        if(in_array('FEMALE', $gender) && !in_array('MALE', $gender)) {

            if(in_array('ADULT', $age)){
                return 'women';
            } 

            elseif(in_array('KID', $age)){
                return 'kgirls';
            }

            elseif(in_array('TEEN', $age)){
                return 'tgirls';
            }

            elseif(in_array('BABY', $age)){
                return 'bgirls';
            }

        }

        elseif(!in_array('FEMALE', $gender) && in_array('MALE', $gender)) {

            if(in_array('ADULT', $age)){
                return 'men';
            } 

            elseif(in_array('KID', $age)){
                return 'kboys';
            }

            elseif(in_array('TEEN', $age)){
                return 'tboys';
            }

            elseif(in_array('BABY', $age)){
                return 'bboys';
            }

        }

        elseif(in_array('FEMALE', $gender) && in_array('MALE', $gender)) {

            if(in_array('ADULT', $age)){
                return 'unia';
            } 

            elseif(in_array('KID', $age)){
                return 'unik';
            }

            elseif(in_array('TEEN', $age)){
                return 'unit';
            }

            elseif(in_array('BABY', $age)){
                return 'unib';
            }

        }

        return null;
    }

    /**
     * Get SKU
     *
     * @return string
     */
    public function getSKU()
    {
        return array_get($this->data, 'model.articleInfo.id');
    }
    
}