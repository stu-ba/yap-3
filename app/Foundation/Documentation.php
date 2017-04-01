<?php

namespace Yap\Foundation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Cache\Repository as Cache;
use Symfony\Component\DomCrawler\Crawler;

class Documentation
{
    /**
     * The filesystem implementation.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The cache implementation.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The symphony crawler instance.
     *
     * @var Crawler
     */
    protected $crawler;

    protected $icons = [
        'note' => 'lightbulb-o',
        'warning' => 'exclamation',
        'video' => 'film'
    ];


    /**
     * Create a new documentation instance.
     *
     * @param  Filesystem $files
     * @param  Cache      $cache
     * @param Crawler     $crawler
     */
    public function __construct(Filesystem $files, Cache $cache, Crawler $crawler)
    {
        $this->files = $files;
        $this->cache = $cache;
        $this->crawler = $crawler;
    }

    /**
     * Get the documentation index page.
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->cache->remember('docs.index', 10, function () {
            $path = base_path('resources/docs/index.md');

            if ($this->files->exists($path)) {
                return markdown($this->files->get($path));
            }

            return null;
        });
    }

    /**
     * Get the given documentation page.
     *
     * @param  string  $page
     * @return string
     */
    public function get($page)
    {
        return $this->cache->remember('docs.'.$page, 10, function () use ($page) {
            $path = base_path('resources/docs/'.$page.'.md');

            if ($this->files->exists($path)) {
                return markdown($this->files->get($path));
            }

            return null;
        });
    }

    /**
     * Check if the given section exists.
     *
     * @param  string  $page
     * @return boolean
     */
    public function sectionExists($page)
    {
        return $this->files->exists(
            base_path('resources/docs/'.$page.'.md')
        );
    }

    public function processFile(string $path): ?string {

    }

}