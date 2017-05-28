<?php

namespace Yap\Foundation\Documentation;

use Illuminate\Filesystem\Filesystem;
use Yap\Exceptions\DocumentationException;

class Maintainer
{

    const THROW = 0x1;
    const SKIP_EXISTS = 0x2;
    const SKIP_DIRECTORY = 0x4;
    const SKIP_WRITABLE = 0x8;

    /**
     * The filesystem implementation.
     *
     * @var Filesystem $files
     */
    protected $files;

    /**
     * Mute git output.
     *
     * @var bool
     */
    protected $quiet = false;


    /**
     * Create a new documentation instance.
     *
     * @param  Filesystem $files
     *
     * @throws DocumentationException
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;

        $path = config('documentation.path');

        if (str_contains($path, '.')) {
            throw new DocumentationException('Path to documentation can not contain \'.\'.');
        } elseif ( ! str_contains(config('documentation.git.repository'), 'github.com')) {
            throw new DocumentationException('Documentation repository must be hosted on github.com.');
        }

        $this->path = base_path($path);
    }


    /**
     * Installs documentation repository.
     *
     * @param bool $force
     *
     * @return bool
     * @throws DocumentationException
     */
    public function install(bool $force = false): bool
    {

        if ( ! $force && $this->check()) {
            throw new DocumentationException('Repository already exists.', 1);
        }

        if ($force) {
            $this->clean();
        }

        $this->files->makeDirectory($this->path, 0755, true, true);
        $this->gitignore();
        $this->clone();

        return true;
    }


    public function check(int $flags = 0): bool
    {
        $throw = function ($message, $code) use ($flags) {
            if ($flags & self::THROW) {
                throw new DocumentationException($message, $code);
            }

            return false;
        };

        if ( ! ($flags & self::SKIP_EXISTS) && ! $this->files->exists($this->path.'/.git')) {
            return $throw('Directory ['.$this->path.'] does not exist.', 2);
        } elseif ( ! ($flags & self::SKIP_DIRECTORY) && ! $this->files->isDirectory($this->path)) {
            return $throw('['.$this->path.'] is not directory.', 3);
        } elseif ( ! ($flags & self::SKIP_WRITABLE) && ! $this->files->isWritable($this->path)) {
            return $throw('Directory ['.$this->path.'] is not writable.', 4);
        }

        return true;
    }


    protected function clean(): bool
    {
        return $this->files->cleanDirectory($this->path);
    }


    /**
     * Generates .gitignore file.
     * If .gitignore exists it checks if entry is already in place.
     */
    protected function gitignore(): void
    {
        $dirname  = $this->files->dirname($this->path);
        $basename = $this->files->basename($this->path);

        $gitignore      = $dirname.'/.gitignore';
        $gitignoreEntry = '/'.$basename."/\n";

        if ($this->files->exists($gitignore)) {
            if ( ! str_contains($this->files->get($gitignore), $gitignoreEntry)) {
                $this->files->append($gitignore, $gitignoreEntry);
            }
        } else {
            $this->files->put($gitignore, $gitignoreEntry);
        }
    }


    protected function clone(): void
    {
        @exec('cd '.$this->path.' && '.$this->cloneCommand().($this->quiet ? ' 2>&1' : ''));
    }


    private function cloneCommand(): string
    {
        return 'git clone --branch '.config('documentation.git.branch').' --depth '.config('documentation.git.depth').' '.config('documentation.git.repository').' .';
    }


    public function update(): bool
    {
        $this->check(self::THROW);
        $this->pull();

        return true;
    }


    protected function pull(): void
    {
        @exec('cd '.$this->path.' && '.$this->pullCommand().($this->quiet ? ' 2>&1' : ''));
    }


    private function pullCommand(): string
    {
        return 'git pull origin '.config('documentation.git.branch');
    }


    /**
     * @return bool
     */
    public function isQuiet(): bool
    {
        return $this->quiet;
    }


    /**
     * @param bool $quiet
     *
     * @return Maintainer
     *
     */
    public function setQuiet(bool $quiet): Maintainer
    {
        $this->quiet = $quiet;

        return $this;
    }

}
