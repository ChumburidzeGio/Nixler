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

    private $translations;

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

        $this->translations = json_decode(file_get_contents(resource_path('docs/crawler/zalando.json')), 1);

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
        return 
            $this->data && 
            array_get($this->data, 'model.articleInfo.active') == true && 
            array_get($this->data, 'model.articleInfo.available') == true && 
            $this->getSKU() !== null && 
            !(!$this->isUK() && !$this->parseEnVersion()->getSKU());
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

            $description = array_get($this->lcen->getRaw(), 'model.articleInfo.attributes');

        } elseif($this->isProduct() && $this->isUK()) {

            $description = array_get($this->data, 'model.articleInfo.attributes');
            
        } else {

            return null;

        }
        
        $description = collect($description)->map(function($item) {
            
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
    public function getEUSize($size)
    {
        if($size == 'One Size'){
            return 'ერთი ზომა';
        }

        $units = array_get($this->lcen->getRaw(), 'model.articleInfo.units');

        foreach ($units as $unit) {
            
            if(array_get($unit, 'size.local') == $size) {

                $sunits = array_get($this->data, 'model.articleInfo.units');

                foreach ($sunits as $sunit) {

                    if(array_get($sunit, 'size.manufacturer') == array_get($unit, 'size.manufacturer')) {

                        return array_get($sunit, 'size.local');

                    }

                }

            }

        }

        return $size;
    }

    /**
     * Parse the variants of product
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->data;
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
                'name' => array_get($item, 'size.local')
            ];

        });
    }

    /**
     * Translate word
     *
     * @return string
     */
    public function translate(string $word, $type = null) : string
    {
        if(!is_null($type)) {

            $wordbase = array_get($this->translations, $type);

            $word = preg_replace_callback_array([
                "/(\d.+)\"/" => function ($match) {
                    return round(floatval($match[1]) / 0.393700787). ' სმ';
                },
                "/Size (\d+|One Size|S|M|S\/M|XS|XS\/S|L|SxAB|CUP B|Sx32)/" => function ($match) {
                    return 'ზომა ' . $this->getEUSize($match[1]);
                },
                "/Our model is (.*) tall and is wearing size (.*)/" => function ($match) {
                    return 'ჩვენი მოდელი არის '.$match[1].' სიმაღლის და იცვავს ზომას '.$this->getEUSize($match[2]);
                },
                "/(\d+%) ([a-z ]+)/" => function ($match) {
                    return $match[1].' '.$this->translate($match[2], 'material');
                }
            ], $word);

            //replace washing details
            if(str_contains($word, 'machine wash') || str_contains($word, 'Machine wash')){

                $word = strtolower($word);

                $word = preg_replace_callback_array([
                    "/machine wash at (\d+&deg;)c/" => function ($match) {
                        return 'მანქანაში რეცხვა '.$match[1].' გრადუსზე';
                    },
                    "/a shrinkage of up to (\d+%) may occur/" => function ($match) {
                        return 'შესაძლებელია მოხდეს ზომაში შემცირება '.$match[1].'-ით';
                    }
                ], $word);

                $word = strtr($word, array_get($this->translations, 'washing'));

            }

            if(array_get($wordbase, $word)) 
            {
                return array_get($wordbase, $word);
            }

            $hasEnglish = app(LanguageDetectService::class)->detect($word)->has('english', 4);

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
            'Loose' => 'თავისუფალი',
            'Spacious inner compartment' => 'ფართე შიგა სივრცე',
            'Top part material' => 'ზედა ნაწილის მასალა',
            'Upper material' => 'ზედა ნაწილის მასალა',
            'Leather and textile' => 'ტყავი და ტექსტილი',
            'Internal material' => 'შიდა მასალა',
            'Insert material' => 'სარჩული',
            'Textile' => 'ტექსტილი',
            'Normal' => 'ნორმალური',
            'Backless' => 'ზურგის გარეშე',
            'Detail' => 'წვრილმანი',
            'Decorative seams' => 'დეკორატიული ნაკერები',
            'Shoe fastener' => '',
            'Laces' => 'თასმები',
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
        if(!$this->isUK() && $this->lcen->isProduct()) return $this->lcen->getCategory();

        $matching = array_get($this->translations, 'silhouetteCodeMatching');

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

        if(!$this->isProduct()) {
            return [];
        }

        $brand = array_get($this->data, 'model.articleInfo.brand.name');

        $color = $this->translate(array_get($this->data, 'model.articleInfo.color'), 'color');

        $category = $this->translate(array_get($this->data, 'model.articleInfo.category_tag'), 'category');

        $silhouetteCode = $this->translate(
            str_replace('_', ' ', title_case(array_get($this->data, 'model.articleInfo.silhouette_code')))
        , 'silhouetteCode');

        return array_flip(compact('color', 'category', 'silhouetteCode', 'brand'));
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