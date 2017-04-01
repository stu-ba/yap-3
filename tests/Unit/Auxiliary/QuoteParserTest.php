<?php

namespace Tests\Unit\Auxiliary;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;
use Yap\Auxiliary\BlockQuoteParser;

class QuoteParserTest extends TestCase
{

    public function testQuoteIconIsReplaced()
    {
        $text = 'Some informative text here.';

        foreach (config('documentation.icons') as $icon => $svg) {
            $html = (new BlockQuoteParser)->text('> {'.$icon.'} '.$text);
            $expected = $this->wrap($icon, $svg, $text);
            $this->assertEquals($expected, $html);
        }
    }


    //Modified function to wrap svg around extra tags
    private function wrap(string $icon, string $svg, string $text): string
    {
        return '<blockquote class="has-icon '.$icon.'"><div class="paragraph"><div class="icon"><span class="svg">'.svg($svg).'</span></div>'.trim($text).'</div></blockquote>';
    }


    public function testMarkdownIsCompiled()
    {
        $files = new Filesystem();
        $stub['md'] = $files->get(base_path('tests/Unit/Auxiliary/Stubs/dummy.md'));
        $stub['html'] = $files->get(base_path('tests/Unit/Auxiliary/Stubs/dummy-compiled.html'));

        $html = (new BlockQuoteParser)->text($stub['md']);

        $this->assertEquals($stub['html'], $html);
    }

}
