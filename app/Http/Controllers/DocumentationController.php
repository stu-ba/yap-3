<?php

namespace Yap\Http\Controllers;

use Symfony\Component\DomCrawler\Crawler;
use Yap\Foundation\Documentation\Compiler;

class DocumentationController extends Controller
{
    /**
     * The documentation repository.
     *
     * @var Compiler
     */
    protected $compiler;

    /**
     * @var Crawler
     */
    protected $crawler;


    /**
     * Create a new controller instance.
     *
     * @param Compiler $compiler
     * @param Crawler  $crawler
     *
     */
    public function __construct(Compiler $compiler, Crawler $crawler)
    {
        $this->compiler = $compiler;
        $this->crawler = $crawler;
    }
    /**
     * Show the root documentation page (/docs).
     *
     * @return Response
     */
    public function showRootPage()
    {
        return redirect()->route('docs');
    }
    /**
     * Show a documentation page.
     *
     * @param  string|null $page
     * @return Response
     */
    public function show($page = null)
    {
        $sectionPage = $page ?: config('documentation.main');
        $content = $this->compiler->get($sectionPage);

        if (is_null($content)) {
            abort(404);
        }

        $this->crawler->add($content);
        $title = $this->crawler->filterXPath('//h1');

        $section = '';
        if ($this->compiler->sectionExists($page)) {
            $section .= '/'.$page;
        } elseif (! is_null($page)) {
            return redirect()->route('docs');
        }

        return view('docs', [
            'title' => count($title) ? $title->text() : null,
            'index' => $this->compiler->getIndex(),
            'content' => $content,
            'currentSection' => $section,
        ]);
    }
}

