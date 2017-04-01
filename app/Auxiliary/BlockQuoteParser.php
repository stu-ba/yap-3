<?php

namespace Yap\Auxiliary;

use ParsedownExtra;

class BlockQuoteParser extends ParsedownExtra
{


    public function __construct()
    {
        parent::__construct();
        array_unshift($this->BlockTypes['>'], 'IconQuote');
        $this->icons = config('documentation.icons');
    }


    protected function blockIconQuote($line)
    {
        if (preg_match('/^>[ ]?(.*)/', $line['text'], $matches)) {
            list($html, $attributes) = $this->insertSvg($matches[1]);

            $block = [
                'extent'      => strlen($matches[0]),
                'interrupted' => true,
                'element'     => [
                    'name'       => 'blockquote',
                    'handler'    => 'line',
                    'attributes' => $attributes,
                    'text'       => $html,
                ],
            ];

            return $block;
        }
    }


    private function insertSvg($string)
    {
        if (preg_match('/\{([^}]+)\}/', $string, $iconMatch)) {
            $string = substr($string, strlen($iconMatch[0]));
            if (array_key_exists($iconMatch[1], $this->icons)) {
                $string = $this->wrapInFlag($iconMatch[1], $string);
                $attributes = ['class' => 'has-icon '.$iconMatch[1]];
            }
        }

        return [$string, $attributes ?? null];
    }


    private function wrapInFlag($svg, $text)
    {
        return '<div class="paragraph"><div class="flag"><span class="svg">'.svg($this->icons[$svg]).'</span></div>'.trim($text).'</div>';
    }

    //protected function getQuoteElement($svg, $text)
    //{
    //    return [
    //        'name'    => 'span',
    //        'handler' => 'elements',
    //        'text'    => [
    //            [
    //                'name'       => 'div',
    //                'handler'    => 'element',
    //                'attributes' => [
    //                    'class' => 'flag'
    //                ],
    //                'text'       => [
    //                    'name'       => 'span',
    //                    'handler'    => 'line',
    //                    'attributes' => [
    //                        'class' => 'svg',
    //                    ],
    //                    'text'       => svg($this->icons[$svg]),
    //                ]
    //            ],
    //            [
    //                'name'    => 'text',
    //                'handler' => 'line',
    //                'text'    => $text,
    //            ],
    //        ],
    //    ];
    //}
}







