<?php

namespace App\Crawler\Traits;

trait HelpersTrait {

    /**
     * @return Crawler
     */
    public function crawler($tag = null)
    {
    	if(!is_null($tag)) {
    		return $this->crawler->filter($tag);
    	}

    	return $this->crawler;
    }

    /**
     * Trim the string and strip tags inside
     */
    public function clean($string)
    {
        if(is_array($string)) {
            return array_filter($string, 'strlen');
        }

        $string = str_ireplace(["<br />","<br>","<br/>", "</p>"], "\n", $string);

        $string = str_replace("&nbsp;", ' ', $string);

    	return trim(strip_tags($string));
    }

    /**
     * Parse meta tag
     */
    public function getMeta(string $prop)
    {
        $content = $this->crawler('meta[property="og:'.$prop.'"]')->first();

        if(!$content->count()){
            $content = $this->crawler('meta[name="'.$prop.'"]')->first();
        }

        if($content->count()) {
            return $this->clean($content->attr('content'));
        }

        return null;
    }

    /**
     * Find category in list of tags or match category with list of subcats
     *
     * @return int|null
     */
    public function findCategory(array $haystack, array $needle)
    { 
        foreach ($needle as $cat => $value) {

            if(is_string($haystack)) {
                
                if(in_array($haystack, $value)) {
                    return $cat;
                } else {
                    continue;
                }

            }
            
            if(!in_array($cat, $haystack)) {
                continue;
            }

            if(!is_array($value)) {
                return $value;
            }

            foreach ($value as $subcat => $subvalue) {
                
                if(in_array($subcat, $haystack)) {
                    return $subvalue;
                }

            }

            return array_get($value, 'default');
        }
    }

    /**
     * Parse text from dom element
     */
    public function textFrom(string $prop)
    {
        $content = $this->crawler($prop)->first();

        if($content->count()) {
            return $this->clean($content->text());
        }

        return null;
    }

    /**
     * Parse text from the list of dom elements
     */
    public function crawleList($el)
    {
        return array_values($this->clean($this->crawler($el)->each(function($item) {
            return $item->text();
        })));
    }

    /**
     * Transform string from html to markdown
     */
    public function markdownify(string $html) : string
    {
    	$markdown = preg_replace_callback("/(<([^.]+)>)([^<]+)(<\\/\\2>)/s", function($matches){

            $text = $matches[3];

            if($matches[2] == 'style' || $matches[2] == 'script') {
                return "";
            }

            if($matches[2] == 'li') {
                return "* {$text}";
            }

            if($matches[2] == 'h3') {
                return "\n# {$text}\n";
            }

            if($matches[2] == 'p') {
                return "\n{$text}";
            }

            if($matches[2] == 'strong' && $matches[2] == 'b') {
                   return "*{$text}*";
            }

            return $text;

        }, $html);

        $stripped = preg_replace('/\s\s+/', "\n", strip_tags($markdown));


        return ltrim($stripped);
    }

    /**
     * Replace special characters in URL
     */
    public function parseUrl(string $url, $sep='+') : string
    {
        return preg_replace('/[[:space:]]+/', '%20', $url);
    }
}
