<?php

namespace App\Crawler\Patterns;

use Goutte\Client;
use App\Crawler\BasePattern;

class DealsGe extends BasePattern {

    /**
     * Parse the category of product
     *
     * @return integer
     */
    public function getCategory()
    {
        $breadcrumb = $this->getTags();

        return $this->findCategory($breadcrumb, [
            'მსხვილი ტექნიკა' => 39,
            'სამზარეულო' => 32,
            'ჭურჭელი' => 32,
            'წვრილი ტექნიკა' => [
                'default' => 32,
                'ტანსაცმლის საპარსი' => 39,
                'თერმოსი' => 46,
            ],
            'დალაგება დასუფთავება' => 39,
            'სხვადასხვა' => [
                'default' => 32,
                'საკერავი მანქანა' => 39,
                'წყლის დისპენსერი' => 39,
                'ფანარი' => 46,
            ],
        ]);

    }

    /**
     * Parse the tags for product
     *
     * @return array
     */
    public function getTags()
    {
        return $this->crawleList('.main-content-container .row .col-md-12 > .well a');
    }

    /**
     * Parse the image of product from meta tags
     *
     * @return array
     */
    public function getMedia()
    {
        $media = $this->crawler('.mini_image_slider .slide.image_row')->each(function($item) {

            $anchor = $item->filter('a')->first();

            return $anchor->count() ? 'https://deals.ge/'.$anchor->attr('href') : null;

        });

        return $this->clean($media);
    }

    /**
     * Parse the description of product from meta tags
     *
     * @return string
     */
    public function getDescription()
    {
        $el = '.main-content-container .row .col-md-12 .col-md-12.bpg_rioni > *:not(span):not(div)';

        $pieces = array_values($this->clean($this->crawler($el)->each(function($item) {

            $text = $this->markdownify($item->html());

            $text = preg_replace("/შეთავაზებაშია:(.*)მიწოდებით(.*)(საქართველოს მასშტაბით|საქართველოში|თბილისში|ადგილზე მიწოდებით)(.|!?)/", '', $text);

            return $text;

        })));

        if(!is_array($pieces) || !count($pieces)) {
            return null;
        }

        $text = $this->clean(implode("\n", $pieces));

        return preg_replace("/შეთავაზებაში შედის((?s).*)საქართველოს მასშტაბით/i", "", $text);
    }

    /**
     * Parse the title of product from meta tags
     *
     * @return string
     */
    public function getTitle()
    {
        $title = parent::getMeta('title');

        $title = str_ireplace(['შეიძინეთ', '- Deals.ge'], '', $title);

        return $this->clean($title);
    }
    
}