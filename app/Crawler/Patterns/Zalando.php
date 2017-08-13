<?php

namespace App\Crawler\Patterns;

use Goutte\Client;
use App\Crawler\BasePattern;
use Symfony\Component\DomCrawler\Crawler;
use App\Crawler\Crawler as LC;
use App\Services\LanguageDetectService;

class Zalando extends BasePattern {

    protected $data;

    protected $lcen;

    private $translations;

    public function parse(Crawler $crawler)
    {
        parent::parse($crawler);

        $this->data = $this->parseJson();

        $this->translations = json_decode(file_get_contents(resource_path('docs/crawler/zalando.json')), 1);

        return $this;
    }

    public function detectProductsOnPage()
    {
        $products = $this->crawler('#catalogItemsListParent .catalogArticlesList_item');

        if(!$products->count()) return [];

        return array_unique($products->each(function($a)
        {
            return $a->filter('a')->link()->getUri();
        }));
    }

    public function parseEnVersion()
    {
        $enUrl = 'https://www.zalando.co.uk/catalog/?qf=1&q='.$this->getRaw('model.articleInfo.id');

        $this->lcen = $this->lcen ?? app(LC::class)->get($enUrl);

        return $this->lcen;
    }

    public function isUK() : bool
    {
        return !!($this->crawler('html')->first()->attr('lang') == 'en-GB');
    }

    public function parseJson()
    {
        if(!$this->crawler('#z-vegas-pdp-props')->count()) return null;

        $json = $this->crawler('#z-vegas-pdp-props')->first()->text();

        $json = ltrim($json, '<![CDATA[');

        $json = rtrim($json, ']>');

        return json_decode($json, 1);
    }

    public function isProduct() : bool
    {
        $isValidProduct = $this->data && 

            $this->getRaw('model.articleInfo.active') == true && 

            $this->getRaw('model.articleInfo.available') == true && 

            $this->getSKU() !== null;

        if(!$this->isUK()) {

            $isValidProduct = $isValidProduct && 
            
                $this->parseEnVersion()->getSKU() && 

                $this->parseEnVersion()->getRaw('model.articleInfo.active') == true && 

                $this->parseEnVersion()->getRaw('model.articleInfo.available') == true;

        }

        return $isValidProduct;

    }

    public function getTitle()
    {
        if(!$this->isUK() && $this->lcen->isProduct()) 
        {
            return $this->lcen->getTitle();
        }

        $name = $this->getRaw('model.articleInfo.name');

        if($this->translate($name, 'title') !== strtolower($name)) 
        {
            $name = ucfirst($this->translate($name, 'title'));
        }

        $brand = $this->getRaw('model.articleInfo.brand.name');

        return "{$name} - {$brand}";
    }

    public function getDescription()
    {
        if(!$this->isUK() && $this->lcen->isProduct()) 
        {
            $description = array_get($this->lcen->getRaw(), 'model.articleInfo.attributes');
        } 
        elseif($this->isProduct() && $this->isUK()) 
        {
            $description = $this->getRaw('model.articleInfo.attributes');
        } 
        else 
        {
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

    public function getMedia()
    {
        return collect($this->getRaw('model.articleInfo.media.images'))->map(function($item) 
        {
            return array_get($item, 'sources.zoom');
        });
    }
    
    public function getRaw($field = null)
    {
        if(is_null($field))
        {
            return $this->data;
        }
        
        return array_get($this->data, $field);
    }

    public function getVariants()
    {
        return collect($this->getRaw('model.articleInfo.units'))->map(function($item) {

            $price = array_get($item, 'displayPrice.price.value');

            $currency = array_get($item, 'displayPrice.price.currency');

            $originalPrice = null;

            if(array_get($item, 'displayPrice.isDiscounted')) 
            {
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

    public function getCategory()
    {
        if(!$this->isUK() && $this->lcen->isProduct()) 
        {
            return $this->lcen->getCategory();
        }

        $matching = array_get($this->translations, 'silhouetteCodeMatching');

        $silhouetteCode = str_replace('_', ' ', title_case($this->getRaw('model.articleInfo.silhouette_code')));

        return array_get($matching, $silhouetteCode);

    }

    public function getTags() : array
    {
        if(!$this->isUK() && $this->lcen->isProduct()) 
        {
            return $this->lcen->getTags();
        }

        if(!$this->isUK() && !$this->lcen->isProduct()) 
        {
            return [];
        }

        $brand = $this->getRaw('model.articleInfo.brand.name');

        $color = $this->translate($this->getRaw('model.articleInfo.color'), 'color');

        $category = $this->translate($this->getRaw('model.articleInfo.category_tag'), 'category');

        $silhouetteCode = $this->translate(
            str_replace('_', ' ', title_case($this->getRaw('model.articleInfo.silhouette_code')))
        , 'silhouetteCode');

        return array_flip(compact('color', 'category', 'silhouetteCode', 'brand'));
    }

    public function getTarget()
    {
        $age = $this->getRaw('model.articleInfo.targetGroups.age');

        $gender = $this->getRaw('model.articleInfo.targetGroups.gender');

        if(in_array('FEMALE', $gender) && !in_array('MALE', $gender)) 
        {
            if(in_array('ADULT', $age))
            {
                return 'women';
            } 
            elseif(in_array('KID', $age))
            {
                return 'kgirls';
            }
            elseif(in_array('TEEN', $age))
            {
                return 'tgirls';
            }
            elseif(in_array('BABY', $age))
            {
                return 'bgirls';
            }
        }
        elseif(!in_array('FEMALE', $gender) && in_array('MALE', $gender)) 
        {
            if(in_array('ADULT', $age))
            {
                return 'men';
            } 
            elseif(in_array('KID', $age))
            {
                return 'kboys';
            }
            elseif(in_array('TEEN', $age))
            {
                return 'tboys';
            }
            elseif(in_array('BABY', $age))
            {
                return 'bboys';
            }
        }
        elseif(in_array('FEMALE', $gender) && in_array('MALE', $gender))
        {
            if(in_array('ADULT', $age))
            {
                return 'unia';
            } 
            elseif(in_array('KID', $age))
            {
                return 'unik';
            }
            elseif(in_array('TEEN', $age))
            {
                return 'unit';
            }
            elseif(in_array('BABY', $age))
            {
                return 'unib';
            }
        }

        return null;
    }

    public function getSKU()
    {
        return $this->getRaw('model.articleInfo.id');
    }

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

        if($currency == 'GBP') 
        {
            $price = ($price + array_get($shippingFees, 'uk')) * array_get($exchangeRates, 'gbp') * $commissionInDouble;
        } 
        elseif ($currency == 'EUR') 
        {
            $price = ($price + array_get($shippingFees, 'it')) * array_get($exchangeRates, 'eur') * $commissionInDouble;
        }

        $price = money(null, $price);

        return str_replace(',', '', $price);
    }

    public function translate(string $word, $type) : string
    {
        $wordbase = array_get($this->translations, $type);

        if(str_contains($word, 'machine wash') || str_contains($word, 'Machine wash') || str_contains($word, 'Hand wash'))
        {
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

        $word = preg_replace_callback_array([
            "/(\d.+)\"/" => function ($match) {
                return round(floatval($match[1]) / 0.393700787). ' სმ';
            },
            "/Size (.*)\)/" => function ($match) {
                return 'ზომა ' . $this->getEUSize($match[1]) . ')';
            },
            "/Our model is (.*) tall and is wearing size (.*)/" => function ($match) {
                return 'ჩვენი მოდელი არის '.$match[1].' სიმაღლის და იცვავს ზომას '.$this->getEUSize($match[2]);
            },
            "/([1-9\/]+) length/" => function ($match) {
                return $match[1].' სიგრძის';
            }
        ], $word);

        if(array_get($wordbase, $word)) 
        {
            $word = array_get($wordbase, $word);
        }

        if($type == 'dvalues') 
        {
            $word = strtr(strtolower($word), array_get($this->translations, 'dvalues'));
        }

        if($type == 'title') 
        {
            $word = strtr(strtolower($word), array_get($this->translations, 'title'));
        }

        $hasEnglish = app(LanguageDetectService::class)->detect($word)->has('english', 4);

        if($hasEnglish) 
        {
            $this->pushTranslation($type, $word);
        }

        //Normalize sizes after lowercasing
        $word = preg_replace_callback_array([
            "/ზომა (.*)\)/" => function ($match) {
                return 'ზომა ' . strtr($match[1], array_get($this->translations, 'sizes')) . ')';
            }
        ], $word);

        return $word;
    }

    public function pushTranslation($tag, $val)
    {
        $tag = "crawler.translations.zalando.{$tag}";

        $config = config($tag, []);

        array_push($config, $val);

        $config = array_values(array_unique($config));

        config([$tag => $config]);
    }

    public function getEUSize($size)
    {
        if($size == 'One Size')
        {
            return 'ერთი ზომა';
        }

        $units = array_get($this->lcen->getRaw(), 'model.articleInfo.units');

        foreach ($units as $unit) 
        {
            if(array_get($unit, 'size.local') == $size) 
            {
                $sunits = $this->getRaw('model.articleInfo.units');

                foreach ($sunits as $sunit) 
                {
                    if(array_get($sunit, 'size.manufacturer') == array_get($unit, 'size.manufacturer')) 
                    {
                        $size = array_get($sunit, 'size.local');
                        
                        return $size;
                    }
                }
            }
        }

        return $size;
    }
    
}