<?php

namespace App\Crawler\Patterns;

use Goutte\Client;
use App\Crawler\BasePattern;
use GuzzleHttp\Client as GuzzleHttp;

class Forever21 extends BasePattern {

	protected $data;

    private $imageFolders = [
        "1_front_750",
        "2_side_750",
        "3_back_750",
        "4_full_750",
        "5_detail_750",
        "6_flat_750",
        "7_additional_750"
    ];

    private $categoryMatching = [
        'dress' => 2,
        'dress_romper' => 2,
        'sets' => 2,
        'top_blouses' => 2,
        'outerwear_coats-and-jackets' => 2,
        'bottoms' => 2,
        'intimates_loungewear' => 2,
        'activewear' => 2,
        'swimwear_all' => 2,
        'acc' => 2,
        'shoes' => 3,
        'top_blouses_b' => 2,
        'Activewear' => 2,
        'acc_hair-ties' => 4,
    ];

	private $targetMatching = [
       'WOMEN' => 'women',
       'PLUS' => 'women',
       'MEN' => 'men',
       'KIDS' => 'tgirls',
    ];

    protected $shortProductTag = '#main #products .pi_container';

    public function parse($url)
    {
    	parent::parse($url);

        $endpoint = 'https://www.forever21.com/us/shop/Catalog/GetProduct?productId=%s';

        $code = $this->getSKU();

    	$this->data = $this->parseJson(sprintf($endpoint, $code));

    	return $this;
    }

    public function detectProductsOnPage($url)
    {
        $baseUrl = 'https://www.forever21.com/us/shop/Catalog/GetProducts';

        $urlParsed = parse_url($url);

        $params = [
            "page" => [
                'pageNo' => 1,
                'pageSize' => 120
            ],
            'filter' => [
                'price' => [
                    'maxPrice' => 250,
                    'minPrice' => 0
                ]
            ],
            'sort' => [
                'sortType' => ""
            ]
        ];

        $fragment = array_get($urlParsed, 'fragment');

        if($fragment)
        {
            $fragment = explode('&', $fragment);

            foreach ($fragment as $fragmentParam) 
            {
                list($param, $value) = explode('=', $fragmentParam);

                if($param == 'pageno')
                {
                    $params['page']['pageNo'] = $value;
                }

                if($param == 'pageSize')
                {
                    $params['page']['pageSize'] = $value;
                }

                if($param == 'filter')
                {
                    $value = explode('|', $value);

                    foreach ($value as $filter) 
                    {
                        list($name, $value) = explode(':', $filter);

                        if($name == 'price')
                        {
                            list($minPrice, $maxPrice) = explode(',', $value);

                            $params['filter']['price'] = compact('maxPrice', 'minPrice');
                        }
                    }
                }
            }
        }

        if(str_contains($url, 'Catalog/Category'))
        {
            $pathParts = explode('/', array_get($urlParsed, 'path'));

            $params = array_merge([
                'brand' => array_get($pathParts, 5),
                'category' => array_get($pathParts, 6)
            ], $params);
        }

        $response = $this->parseJson($baseUrl, [
            'form_params' => $params
        ]);

        return array_map(function($item) {
            return array_get($item, 'ProductShareLinkUrl');
        }, array_get($response, 'CatalogProducts'));
    }

    public function getTitle()
    {
        $title = $this->data('DisplayName');

        $param = ucfirst(strtolower(
            $this->getParam()
        ));

    	return "{$title} {$param}";
    }

    public function getDescription()
    {
        if(!$description = $this->data('Description'))
        {
            return null;
        }

    	return $this->translate(
            $this->markdownify($description)
        );
    }

    public function getMedia()
    {
        foreach ($this->data('Variants') as $value) {
            
            if($value['ColorName'] == $this->param)
            {
                $folders = $value['ImageFolders'];

                $colorId = $value['ColorId'];

                break;
            }
        }

    	$media = $this->imageFolders;

        $itemCode = $this->data('ItemCode');

    	for ($i = strlen($folders) - 1; $i >= 0; $i--) 
    	{
            if ($folders[$i] != 'Y')
            {
            	unset($media[$i]);
            }
        }

        return array_values(array_map(function($item) use ($itemCode, $colorId) {
        	return "https://www.forever21.com/images/{$item}/{$itemCode}-{$colorId}.jpg";
        }, $media));
    	
    }

    public function getVariants()
    {
    	foreach ($this->data('Variants') as $value) {
            
            if($value['ColorName'] == $this->param)
            {
                $variants = $value['Sizes'];

                break;
            }
        }

        $price = $this->getPrice();

        $originalPrice = $this->getOriginalPrice();

        return array_map(function ($item) use ($price, $originalPrice) {

            return [
                'original_price' => $originalPrice,
                'price' => $price,
                'in_stock' => $item['Available'] ? 5 : 0,
                'name' => array_get($item, 'SizeName')
            ];

        }, $variants);
    }

    public function getCategory()
    {
        $category = $this->data('CategoryName');

        $parentCategory = $this->data('PrimaryParentCategory');

        return array_get($this->categoryMatching, $category, 
                    array_get($this->categoryMatching, $parentCategory));
    }

    public function getPrice()
    {
    	return $this->calculatePrice('US', 'GE', $this->data('ListPrice'));
    }

    public function getOriginalPrice()
    {
        $value = $this->calculatePrice('US', 'GE', $this->data('OriginalPrice'));

        if($this->getPrice() < $value)
        {
            return $value;
        }

        return null;
    }

    public function getTags()
    {
        
    }

    public function getTarget()
    {
        $sizeChart = $this->data('ProductSizeChart');
        
        return array_get($this->targetMatching, $sizeChart);
    }

    public function getAvailableParams()
    {
        if(!$variants = $this->data('Variants'))
        {
            return [];
        }

        return array_map(function($item) {
            return array_get($item, 'ColorName');
        }, $variants);
    }

    public function getParam()
    {
        return $this->param;
    }

    public function getSKU()
    {
        return filter_var($this->textFrom('#tabDescriptionContent .t_small'), FILTER_SANITIZE_NUMBER_INT);
    }

    private function data($key, $default = null)
    {
    	return array_get($this->data, $key, $default);
    }

    private function parseJson($url, $options = [])
    {
        $json = app(GuzzleHttp::class)->request('POST', $url, $options);

        return json_decode($json->getBody(), 1);
    }

    private function translate($text)
    {
        $lines = array_map(function($line) {

            $line = trim($line);

            $line = strtr($line, [
                '# details' => '# დეტალები',
                '# Content + Care' => '# შემადგენლობა და მოვლა',
                '# Size + Fit' => '# ზომა და მორგება',

                '- This is an independent brand and not a Forever 21 branded item.' => '',
            ]);

            return $line;

        }, explode(PHP_EOL, $text));

    	return implode(PHP_EOL, $lines);
    }

}