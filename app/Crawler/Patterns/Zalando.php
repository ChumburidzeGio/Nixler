<?php

namespace App\Crawler\Patterns;

use Goutte\Client;
use App\Crawler\BasePattern;
use Symfony\Component\DomCrawler\Crawler;
use App\Crawler\Crawler as LC;
use Symfony\Component\DomCrawler\Link;
use App\Services\LanguageDetectService;

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

        $this->data = $this->parseJson();

        return $this;
    }

    /**
     * Check if its Zalando.co.uk
     *
     * @return bool
     */
    public function parseEnVersion()
    {
        $enUrl = 'https://www.zalando.co.uk/catalog/?qf=1&q=';

        $this->lcen = $this->lcen ?? app(LC::class)->get($enUrl.array_get($this->data, 'model.articleInfo.id'));

        return $this->lcen;
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
     * 
     *
     * @return bool
     */
    public function parseJson()
    {
        if(!$this->crawler('#z-vegas-pdp-props')->count()) {
            return null;
        }

        $json = $this->crawler('#z-vegas-pdp-props')->first()->text();

        $json = ltrim($json, '<![CDATA[');

        $json = rtrim($json, ']>');

        return json_decode($json, 1);
    }

    /**
     * Check if we are on product page
     *
     * @return boolean
     */
    public function isProduct() : bool
    {
        return $this->data && $this->getSKU() !== null && !(!$this->isUK() && !$this->parseEnVersion()->getSKU());
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
        if(!$this->isUK() && $this->lcen->isProduct()) {

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
        if(!$this->isUK() && $this->lcen->isProduct()) {

            return $this->lcen->getDescription();

        }
        
        $description = collect(array_get($this->data, 'model.articleInfo.attributes'))->map(function($item) {
            
            $header = $this->translate(array_get($item, 'category'), 'headings');

            $data = [];

            foreach (array_get($item, 'data') as $key => $item) {
                
                if(array_get($item, 'name') == 'config_sku' || !array_get($item, 'name') || !array_get($item, 'values')) {
                    continue;
                }

                $data[] = $this->translate(array_get($item, 'name'), 'dname').': '.$this->translate(array_get($item, 'values'), 'dvalues');

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

            $price = array_get($item, 'displayPrice.price.value');

            $currency = array_get($item, 'displayPrice.price.currency');

            $originalPrice = null;

            if(array_get($item, 'displayPrice.isDiscounted')) {

                $originalPrice = array_get($item, 'displayPrice.originalPrice.value');

            }

            return [
                'original_price' => $originalPrice ? $this->calcPrice($originalPrice, $currency) : null,
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

            $size = $size;

        }

        if($size == 'One Size') {
            return 'ერთი';
        }

        return $size;
    }

    /**
     * Translate word
     *
     * @return string
     */
    public function translate(string $word, $type = null) : string
    {
        $translations = json_decode(file_get_contents(resource_path('docs/crawler/zalando.json')), 1);

        if(!is_null($type)) {

            $wordbase = array_get($translations, $type);

            //replace inches with cm
            $word = preg_replace_callback("/(\d.+)\"/", function($matches){
                return round(floatval($matches[1]) / 0.393700787). ' სმ';
            }, $word);

            //replace size and transform to europian TODO: Sx32
            $word = preg_replace_callback("/Size (\d+|One Size|S|M|S/M|XS|XS/S|L|SxAB|CUP B|Sx32)/", function($matches){
                return 'ზომა ' . $this->transfromSize(array_get($matches, 1), 'UK');
            }, $word);

            //replace model info
            $word = preg_replace_callback("/Our model is (.*) tall and is wearing size (.*)/", function($matches){
                return 'ჩვენი მოდელი არის '.array_get($matches, 2).' სიმაღლის და იცვავს ზომას '.$this->transfromSize(array_get($matches, 3), 'UK');
            }, $word);

            //replace matterials
            $word = preg_replace_callback("/(\d+%) ([a-z ]+)/", function($matches){
                return array_get($matches, 1).' '.$this->translate(array_get($matches, 2), 'material');
            }, $word);

            //replace washing details
            if(str_contains($word, 'machine wash') || str_contains($word, 'Machine wash')){

                $word = strtolower($word);

                $word = preg_replace_callback("/machine wash at (\d+&deg;)c/", function($matches){
                    return 'მანქანაში რეცხვა '.array_get($matches, 1).' გრადუსზე';
                }, $word);

                $word = preg_replace_callback("/a shrinkage of up to (\d+%) may occur/", function($matches){
                    return 'შესაძლებელია მოხდეს ზომაში შემცირება '.array_get($matches, 1).'-ით';
                }, $word);

                $word = strtr($word, [
                    'machine wash on gentle cycle' => 'მანქანაში რეცხვა დელიკატურზე',
                    'do not tumble dry' => 'არ გააშროთ სარეცხ მანქანაში',
                    'do not iron' => 'არ გააუთაოთ',
                    'dry clean only' => 'მხოლოდ ქიმწმენდა',
                    'colour may run - please wash separately' => 'შესაძლებელია გადაუვიდეს ფერი - გარეცხეთ ცალკე',
                    'do not wash' => 'არ გარეცხოთ',
                    'dry cleanable' => 'შესაძლებელია ქიმწმენდა',
                    'tumble dry' => 'გააშრეთ სარეცხ მანქანაში',
                    'machine wash on wool cycle' => 'მანქანაში გარეცხეთ ბამბის ციკლზე',
                ]);

            }

            if(array_get($wordbase, $word)) 
            {
                return array_get($wordbase, $word);
            }

            $hasEnglish = app(LanguageDetectService::class)->detect($word)->has('english');

            if($hasEnglish) {
                $this->pushTranslation($type, $word);
            }

        }

        $word = array_get([
            'Zip' => 'ელვა',
            'Floral' => 'ყვავილებიანი',
            'Long' => 'გრძელი',
            'Cropped' => 'გადაჭრილი',
            'Round neck' => 'მომრგვალებული საყელო',
            'Sleeveless' => 'უსახელო',
            'Calf-length' => 'მუხლს ქვემოთ',
            'Low V-neck' => 'დაბალი V-ტიპის ჭრილი',
            'Outer material' => 'გარე მასალა',
            'Faux leather' => 'ხელოვნური ტყავი',
            'Fabric' => 'ნაჭერი',
            'Synthetic leather' => 'სინთეტიკური ტყავი',
            'Magnet' => 'მაგნიტი',
            'Mobile phone pocket' => 'ტელეფონის ჯიბე',
            'Carrying handle' => 'სახელური',
            'Collar' => 'საყელო',
            'Adjustable straps' => 'რეგულირებადი თასმები',
            'Extra short' => 'ძალიან მოკლე',
            'Asymmetrical' => 'ასიმეტრიული',
            'Side pockets' => 'გვერდითი ჯიბეები',
            'Elasticated waist' => 'ელასტიური წელი',
            'Inner leg length' => 'ფეხის სიგრძე შიგნიდან',
            'Outer leg length' => 'ფეხის სიგრძე გარედან',
            'Loose' => 'თავისუფალი',
            'Spacious inner compartment' => 'ფართე შიგა სივრცე',
            'Top part material' => 'ზედა ნაწილის მასალა',
            'Upper material' => 'ზედა ნაწილის მასალა',
            'Leather and textile' => 'ტყავი და ტექსტილი',
            'Internal material' => 'შიდა მასალა',
            'Insert material' => 'სარჩული',
            'Cover sole' => 'ძირის საფარი',
            'Sole' => 'ძირი',
            'Textile' => 'ტექსტილი',
            'Normal' => 'ნორმალური',
            'Backless' => 'ზურგის გარეშე',
            'Detail' => 'წვრილმანი',
            'Decorative seams' => 'დეკორატიული ნაკერები',
            'Shoe fastener' => '',
            'Laces' => 'თასმები',

            'Trousers' => 'შარვალები',
            'Trouser' => 'შარვალი',
            'Sports shoes' => 'სპორტული ფეხსაცმელი',
            'Scarf' => 'შარფი',
            'Dress' => 'კაბა',
            'Summer dress' => 'საზაფხულო კაბა',
            'Shirt' => 'პერანგი',
            'Shorts' => 'შორტები',
            'Ballerina Shoe' => 'ბალეტკები',
            'Tailored' => 'მკაცრ სტილში გაფორმებული',
        ], $word, $word);

        $word = strtr($word, [
            'Semi-sheer' => 'ნახევრად გამჭვირვალე',
            'Flat' => 'ფართე',
            'Short' => 'მოკლე',
            'slip' => 'სრიალა',
            'deep pockets' => 'ღრმა ჯიბეები',
            'Deep pockets' => 'ღრმა ჯიბეები',
            'Treat with a suitable protector before wear' => 'დაამუშავეთ შესაბამისი დამცავით ჩაცმამდე',
            'bust darts' => 'მკერდის დამჭერი',
            'Regular' => 'ჩვეულებრივი',
            'Print' => 'პრინტი',
            'Button' => 'ღილი',
            'Plain' => 'გლუვი',
            'Zip fastening' => 'ელვა შესაკრავი',
            'Knee-length' => 'მუხლამდე სიგრძე',
            'Heel type' => 'ქუსლის ტიპი',
            'Jersey' => 'ჯერსი',
            'Lace' => 'მაქმანი',
        ]);

        $word = preg_replace_callback("/((\d+) (Size))/", function($matches){
            return array_get($matches, 3) . ' ' . $this->transfromSize(array_get($matches, 2), 'UK');
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
        if(!$this->isUK() && $this->lcen->isProduct()) {

            return $this->lcen->getCategory();

        }

        $matching = [
            "Bag" => 8,
            "Dress" => 2,
            "One Piece Suit" => 2,
            "Pullover" => 2,
            "Shirt" => 2,
            "Trouser" => 2,
            "T Shirt Top" => 2,
            "One Piece Beachwear" => 2,
            "Bustier" => 2,
            "Nightwear Combination" => 2,
            "Beach Trouser" => 2,
            "Skirt" => 2,
            "Bra" => 2,
            "Cardigan" => 2,
            "Underpant" => 2,
            "Coat" => 2,
            "Combination Clothing" => 2,
            "Bikini Combination" => 2,
            "Beach Shirt" => 2,
            "Vest" => 2,
            "Sneaker" => 3,
            "Low Shoe" => 3,
            "Sandals" => 3,
            "First Shoe" => 3,
            "Ballerina Shoe" => 3,
            "Ankle Boots" => 3,
            "Nightdress" => 2,
            "One Piece Underwear" => 2,
            "Jacket" => 2,
            "Other Accessoires" => 4,
            "Backless Slipper" => 3,
            "Boots" => 3,
            "Beach Accessoires" => 2,
            "Stocking" => 2,
            "Headgear" => 2,
            "Other Equipment" => null,
            "Backpack" => 8,
            "Glasses" => 4,
            "Watch" => 6,
            "Scarf" => 4,
            "Bathrobe" => 2,
            "Underwear Combination" => 2,
            "Night Shirt" => 2,
            "One Piece Nightwear" => 2,
            "Night Trouser" => 2,
            "Undershirt" => 2,
            "Suit Accessoires" => 4,
            "Tights" => 2,
            "Pumps" => 3,
            "Wallet" => 4,
            "Bikini Top" => 2,
            "Corsage" => 2
        ];

        $silhouetteCode = str_replace('_', ' ', title_case(array_get($this->data, 'model.articleInfo.silhouette_code')));

        return array_get($matching, $silhouetteCode);

    }

    /**
     * Parse the tags of product
     *
     * @return array
     */
    public function getTags() : array
    {
        if(!$this->isUK() && $this->lcen->isProduct()) {

            return $this->lcen->getTags();

        }

        $color = array_get($this->data, 'model.articleInfo.color');
        $category = array_get($this->data, 'model.articleInfo.category_tag');
        $silhouetteCode = str_replace('_', ' ', title_case(array_get($this->data, 'model.articleInfo.silhouette_code')));

        $color = $this->translate($color, 'color');
        $category = $this->translate($category, 'category');
        $silhouetteCode = $this->translate($silhouetteCode, 'silhouetteCode');

        return array_flip(compact('color', 'category', 'silhouetteCode'));
    }

    /**
     * Parse the tags of product
     *
     * @return array
     */
    public function pushTranslation($tag, $val)
    {
        $tag = "crawler.translations.zalando.{$tag}";

        $config = config($tag, []);

        array_push($config, $val);

        $config = array_values(array_unique($config));

        config([$tag => $config]);
    }

    /**
     * Calculate price
     *
     * @return int
     */
    public function calcPrice(float $price, string $currency) : int
    {
        $exchangeRates = [
            'eur' => 2.80,
            'gbp' => 3.15
        ];

        $shippingFees = [
            'it' => 4,
            'uk' => 5,
        ];

        $commissionInDouble = (10 + 100) / 100;

        if($currency == 'GBP') {

            $price = ($price + array_get($shippingFees, 'uk')) * array_get($exchangeRates, 'gbp') * $commissionInDouble;

        } elseif ($currency == 'EUR') {

            $price = ($price + array_get($shippingFees, 'it')) * array_get($exchangeRates, 'eur') * $commissionInDouble;

        }

        $price = money(null, $price);

        return str_replace(',', '', $price);
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