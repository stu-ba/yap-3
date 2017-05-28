<?php

namespace Yap\Foundation\Documentation;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Filesystem\Filesystem;

class Compiler
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
     * The cache length in minutes.
     *
     * @var integer
     */
    protected $cacheFor;

    /**
     * Path to documentation files.
     *
     * @var string
     */
    protected $path;


    /**
     * Create a new documentation instance.
     *
     * @param  Filesystem $files
     * @param  Cache      $cache
     */
    public function __construct(Filesystem $files, Cache $cache)
    {
        $this->files     = $files;
        $this->cache     = $cache;
        $this->cache_for = config('documentation.cache_for', 10);
        $this->path      = config('documentation.path');
    }


    /**
     * Get the documentation index page.
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->cache->remember('docs.index', $this->cache_for, function () {
            $path = base_path($this->path.'index.md');

            if ($this->files->exists($path)) {
                return markdown($this->files->get($path));
            }

            return null;
        });
    }


    /**
     * Get the given documentation page.
     *
     * @param  string $page
     *
     * @return string
     */
    public function get($page)
    {
        return $this->cache->remember('docs.'.$page, $this->cache_for, function () use ($page) {
            $path = base_path($this->path.$page.'.md');

            if ($this->files->exists($path)) {
                return markdown($this->files->get($path));
            }

            return null;
        });
    }


    /**
     * Check if the given section exists.
     *
     * @param  string $page
     *
     * @return boolean
     */
    public function sectionExists($page)
    {
        return $this->files->exists(base_path($this->path.$page.'.md'));
    }
}
