<?php

namespace App\Services;

use Parsedown;

class Markdown extends Parsedown
{
	protected $BlockTypes = array(
        '#' => array('Header'),
        '*' => array('Rule', 'List'),
        '-' => array('Rule'),
        '`' => array('FencedCode'),
        '~' => array('FencedCode'),
    );

	function text($text)
    {
        # make sure no definitions are set
        $this->DefinitionData = array();

        # standardize line breaks
        $text = strip_tags(str_replace(array("\r\n", "\r"), "\n", $text));

        # remove surrounding line breaks
        $text = trim($text, "\n");

        # split text into lines
        $lines = explode("\n", $text);

        # iterate through lines to identify blocks
        $markup = $this->lines($lines);

        # trim line breaks
        $markup = trim($markup, "\n");

        return $markup;
    }


    #
    # Header

    protected function blockHeader($Line)
    {
        if (isset($Line['text'][1]))
        {
            $text = trim($Line['text'], '# ');

            $Block = array(
                'element' => array(
                    'name' => 'h4',
                    'text' => $text,
                    'handler' => 'line',
                ),
            );

            return $Block;
        }
    }

    protected $StrongRegex = array(
        '*' => '/^[*]((?:\\\\\*|[^*]|[*][*][^*]+?[*][*])+?)[*](?![*])/s',
        //'_' => '/^__((?:\\\\_|[^_]|_[^_]*_)+?)__(?!_)/us',
    );

    protected $EmRegex = array(
        //'*' => '/^[*]((?:\\\\\*|[^*]|[*][*][^*]+?[*][*])+?)[*](?![*])/s',
        '_' => '/^_((?:\\\\_|[^_]|__[^_]*__)+?)_(?!_)\b/us',
    );

    protected function inlineEmphasis($Excerpt)
    {
        if ( ! isset($Excerpt['text'][1]))
        {
            return;
        }

        $marker = $Excerpt['text'][0];

        if ($marker == '*' && preg_match($this->StrongRegex['*'], $Excerpt['text'], $matches))
        {
            $emphasis = 'strong';
        }
        elseif ($marker == '_' && preg_match($this->EmRegex['_'], $Excerpt['text'], $matches))
        {
            $emphasis = 'em';
        }
        else
        {
            return;
        }

        return array(
            'extent' => strlen($matches[0]),
            'element' => array(
                'name' => $emphasis,
                'handler' => 'line',
                'text' => $matches[1],
            ),
        );
    }

    protected function inlineStrikethrough($Excerpt)
    {
        return;
    }

}