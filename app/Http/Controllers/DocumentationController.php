<?php

namespace Yap\Http\Controllers;

use Symfony\Component\DomCrawler\Crawler;
use Yap\Foundation\Documentation;

class DocumentationController extends Controller
{
    /**
     * The documentation repository.
     *
     * @var Documentation
     */
    protected $docs;

    /**
     * @var Crawler
     */
    protected $crawler;


    /**
     * Create a new controller instance.
     *
     * @param  Documentation $docs
     * @param Crawler        $crawler
     */
    public function __construct(Documentation $docs, Crawler $crawler)
    {
        $this->docs = $docs;
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
        $sectionPage = $page ?: config('documentation.home_doc');
        $content = $this->docs->get($sectionPage);

        if (is_null($content)) {
            abort(404);
        }

        $this->crawler->add($content);
        $title = $this->crawler->filterXPath('//h1');

        $section = '';
        if ($this->docs->sectionExists($page)) {
            $section .= '/'.$page;
        } elseif (! is_null($page)) {
            return redirect()->route('docs');
        }

        return view('docs', [
            'title' => count($title) ? $title->text() : null,
            'index' => $this->docs->getIndex(),
            'content' => $content,
            'currentSection' => $section,
        ]);
    }
}

