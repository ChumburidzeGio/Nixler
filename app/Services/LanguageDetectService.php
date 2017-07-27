<?php

namespace App\Services;

use App\Notifications\ExceptionThrown;
use Exception, Debugbar;

class LanguageDetectService
{
    /**
     * Public function to retrive language from text
     *
     * @return string|null
     */
    public function detect($text) {

        $tokens = $this->tokenize($text);

        $languages = $this->checkForEachLanguage($tokens);

        if(array_first($languages) == 0) {
            return null;
        }

        reset($languages);

        return key($languages);

    }

    /**
     * Tokenize text by spaces and return first 3 tokens
     *
     * @return array
     */
    private function tokenize($text) {

        return explode(' ', $text);

    }

    /**
     * Check for each language the text
     *
     * @return array
     */
    private function checkForEachLanguage($tokens) {

        $languages = [
            'georgian' => 0,
            'russian' => 0,
            'english' => 0
        ];

        foreach ($tokens as $token) {
            
            foreach ($languages as $lang => $passedCharacters) {
                
                $languages[$lang] += $this->checkForLanguage($lang, $token);

            }

        }

        arsort($languages, SORT_NUMERIC);

        return $languages;

    }

    /**
     * Check for seleted language
     *
     * @return array
     */
    private function checkForLanguage($lang, $token) {

        $passedCharacters = 0;

        $stringLength = strlen($token);

        $letters = preg_split("//u", $token, -1, PREG_SPLIT_NO_EMPTY);

        $functionName = sprintf("get%sCharacters", ucfirst($lang));

        $characters = $this->$functionName();

        foreach ($letters as $letter) {

            if(in_array($letter, $characters)) {
                $passedCharacters += 1;
            }

        }

        return $passedCharacters;

    }

    /**
     * Check if text is Georgian
     *
     * @return array
     */
    private function getGeorgianCharacters() {

        return [
            'ა', 'ბ', 'გ', 'დ', 'ე', 'ვ', 'ზ', 'თ', 'ი', 'კ', 'ლ', 
            'მ', 'ნ', 'ო', 'პ', 'ჟ', 'რ', 'ს', 'ტ', 'უ', 'ფ', 'ქ', 
            'ღ', 'ყ', 'შ', 'ჩ', 'ც', 'ძ', 'ჭ', 'ხ', 'ჯ', 'ჰ'
        ];

    }

    /**
     * Check if text is English
     *
     * @return array
     */
    private function getEnglishCharacters() {

        return [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 
            'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 
            'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
        ];

    }

    /**
     * Check if text is Russian
     *
     * @return array
     */
    private function getRussianCharacters() {

        return [
            'А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'Ё', 'ё', 
            'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 
            'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 
            'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш', 'Щ', 'щ', 'Ъ', 'ъ', 
            'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я'
        ];

    }

}