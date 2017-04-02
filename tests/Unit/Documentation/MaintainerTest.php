<?php

namespace Tests\Unit\Documentation;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;
use Yap\Exceptions\DocumentationException;
use Yap\Foundation\Documentation\Maintainer as MaintainerOriginal;

class MaintainerTest extends TestCase
{

    /**
     * @var Maintainer $maintainer
     */
    protected $maintainer;


    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->maintainer = resolve(Maintainer::class);
        $this->maintainer->setPath('tests/Unit/Documentation/Stubs/docs/');
    }


    public function testGitignoreIsAdded()
    {
        /** @var Filesystem $files */
        $files = resolve(\Illuminate\Filesystem\Filesystem::class);
        $this->maintainer->setPath('tests/Unit/Documentation/Stubs/downloads/');
        $this->maintainer->gitignore();
        $this->assertTrue($files->exists(base_path('tests/Unit/Documentation/Stubs/')));
        $files->deleteDirectory(base_path('tests/Unit/Documentation/Stubs/.gitignore'));
    }


    public function testGitignoreIsUpdated()
    {
        /** @var Filesystem $files */
        $files = resolve(\Illuminate\Filesystem\Filesystem::class);
        $gitignore = base_path('tests/Unit/Documentation/Stubs/.gitignore');
        $this->maintainer->setPath('tests/Unit/Documentation/Stubs/downloads/');
        $files->put($gitignore, "/joe\n");
        $this->maintainer->gitignore();

        $this->assertEquals("/joe\n/downloads/\n", $files->get($gitignore));
        $files->deleteDirectory($gitignore);
    }


    public function testInstall()
    {
        $this->markTestSkipped('Test uses \'git clone\', run only if needed.');
        /** @var Filesystem $files */
        $files = resolve(\Illuminate\Filesystem\Filesystem::class);
        $this->maintainer->setPath('tests/Unit/Documentation/Stubs/downloads/');
        $this->maintainer->install();

        $this->assertTrue($files->exists(base_path('tests/Unit/Documentation/Stubs/downloads/.git')));
        $this->assertTrue($files->exists(base_path('tests/Unit/Documentation/Stubs/.gitignore')));

        //clean up
        $files->deleteDirectory(base_path('tests/Unit/Documentation/Stubs/downloads'));
        $files->deleteDirectory(base_path('tests/Unit/Documentation/Stubs/.gitignore'));
    }


    public function testPull()
    {
        $this->markTestIncomplete('Test uses \'git pull\', run only if needed. INCOMPLETE');
        $this->maintainer->setPath('tests/Unit/Documentation/Stubs/downloads/');
        $this->maintainer->install();
        $this->maintainer->update();
        //TODO: some assertion... not sure
    }


    public function testCheckThrows()
    {
        $this->maintainer->setPath(str_random(32));
        $this->expectException(DocumentationException::class);
        $this->expectExceptionCode(2);

        $this->maintainer->check(Maintainer::THROW);
    }


    public function testCheckSkipsExists()
    {
        $this->maintainer->setPath(str_random(32));
        $this->expectException(DocumentationException::class);
        $this->expectExceptionCode(3);

        $this->maintainer->check(Maintainer::THROW | Maintainer::SKIP_EXISTS);
    }


    public function testCheckSkipsDirectory()
    {
        $this->maintainer->setPath(str_random(32));
        $this->expectException(DocumentationException::class);
        $this->expectExceptionCode(4);

        $this->maintainer->check(Maintainer::THROW | Maintainer::SKIP_EXISTS | Maintainer::SKIP_DIRECTORY);
    }


    public function testCheckSkipsWritable()
    {
        $this->maintainer->setPath(str_random(32));
        $this->maintainer->check(Maintainer::SKIP_EXISTS | Maintainer::SKIP_DIRECTORY | Maintainer::SKIP_WRITABLE);
    }
}

class Maintainer extends MaintainerOriginal
{

    //For testing purpose only
    public function setPath($path)
    {
        $this->path = base_path($path);
    }

    //Testing protected method
    public function gitignore(): void
    {
        parent::gitignore();
    }
}
