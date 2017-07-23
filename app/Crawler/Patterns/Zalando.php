<?php

namespace App\Crawler\Patterns;

use Goutte\Client;
use App\Crawler\BasePattern;
use Symfony\Component\DomCrawler\Crawler;

class Zalando extends BasePattern {

    protected $data;

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

        $json = $this->crawler('#z-vegas-pdp-props')->first()->text();

        $json = ltrim($json, '<![CDATA[');

        $json = rtrim($json, ']>');

        $this->data = json_decode($json, 1);

        return $this;
    }

    /**
     * Parse the title for product
     *
     * @return string
     */
    public function getTitle()
    {
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
        $description = collect(array_get($this->data, 'model.articleInfo.attributes'))->map(function($item) {
            
            $header = $this->translate(array_get($item, 'category'));

            $data = [];
            
            foreach (array_get($item, 'data') as $item) {
                
                if(array_get($item, 'name') == 'config_sku') {
                    continue;
                }

                $data[] = $this->translate(array_get($item, 'name')).': '.array_get($item, 'values');

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
     * @return string
     */
    public function transfromSize($size, $type)
    {
        if($type == 'UK')  {

            $size = $size + 28;

        }

        elseif($type == 'US')  {

            $size = ($size == 1) ? 32 : $size + 32;

        }

        return $size;
    }

    /**
     * Translate word
     *
     * @return string
     */
    public function translate($word)
    {
        return array_get([
            'heading_material' => 'ქსოვილი',
            'heading_details' => 'დეტალები',
            'heading_measure_and_fitting' => 'ზომა',
            'Composizione' => 'შემადგენლობა',
            'Outer fabric material' => 'შემადგენლობა',
            'Avvertenze di lavaggio' => 'რეცხვა',
            'Washing instructions' => 'რეცხვა',
            'Scollo' => 'საყელო',
            'Neckline' => 'საყელო',
            'Sheer' => 'გამჭვირვალე',
            'Trasparenza' => 'გამჭვირვალე',
            'Pattern' => 'მოხატულობა',
            'Fantasia' => 'მოხატულობა',
            'Details' => 'დეტალები',
            'Dettagli' => 'დეტალები',
            'Our model\'s height' => 'მოდელის სიმაღლე',
            'Altezza del modello' => 'მოდელის სიმაღლე',
            'Fit' => 'მორგება',
            'Vestibilit&agrave;' => 'მორგება',
            'Length' => 'სიგრძე',
            'Lunghezza' => 'სიგრძე',
            'Sleeve length' => 'სახელოების სიგრძე',
            'Lunghezza manica' => 'სახელოების სიგრძე',
            'Back width' => 'ზურგის სიფართე',
            'Lunghezza delle maniche' => 'სახელოების სიგრძე',
            'Larghezza dello schienale' => 'ზურგის სიფართე',
            'Total length' => 'სრული სიგრძე',
            'Lunghezza totale' => 'სრული სიგრძე',
            'Incrociato' => 'გადაჯვარედინებული',
            'Leggera' => 'ნათელი',
            'Monocromo' => 'მონოქრომული',
            'Cucitura sul seno' => 'სამკუთხედი გული',
            'Gessati' => 'წვრილი',
            'Lunghezza normale' => 'ნორმალური სიგრძე',
        ], $word, $word);
    }

    /**
     * Translate word
     *
     * @return string
     */
    public function calcPrice($price, $currency)
    {
        $EUR2GEL = 2.80;

        $GBP2GEL = 3.15;

        $FEE = 15;

        if($currency == 'GBP') {

            $price = money(null, $price * $GBP2GEL);

        } elseif ($currency == 'EUR') {

            $price = money(null, $price * $EUR2GEL);

        }

        return $price + $FEE;
    }
    
}