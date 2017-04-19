<?php

namespace Tests\Unit\Documentation;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;
use Yap\Foundation\Documentation\Compiler as CompilerOriginal;

class CompilerTest extends TestCase
{

    /**
     * @var Documentation $compiler
     */
    protected $compiler;


    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->compiler = resolve(Compiler::class);
        $this->compiler->setPath('tests/Unit/Documentation/Stubs/docs/');
    }


    public function testDocumentation()
    {
        $pages = [
            'administrator'       => 'Administrator',
            'integration-testing' => 'Integration Testing',
            'team-leader'         => 'Team Leader',
            'team-member'         => 'Team Member',
            'unit-testing'        => 'Unit Testing',
        ];

        $files = new Filesystem();

        $stub = $files->get(base_path('tests/Unit/Documentation/Stubs/docs/compiled-stub.html'));

        foreach ($pages as $page => $title) {
            $compiled = str_replace('{{name}}', $title, $stub);
            $html     = $this->compiler->get($page);
            $this->assertEquals($compiled, $html);
        }
    }


    public function testDocumentationIndex()
    {
        $prospects = [
            '<li>Testing Stub',
            '<li><a href="/docs/unit-testing">Unit Testing</a></li>',
            '<li><a href="/docs/integration-testing">Integration Testing</a></li>',
            '<li>User Guide',
            '<li><a href="/docs/team-member">Team Member</a></li>',
            '<li><a href="/docs/team-leader">Team Leader</a></li>',
            '<li><a href="/docs/administrator">Administrator</a></li>',
        ];

        $index = $this->compiler->getIndex();

        foreach ($prospects as $prospect) {
            $this->assertContains($prospect, $index);
        }
    }
}

class Compiler extends CompilerOriginal
{

    public function setPath($path)
    {
        $this->path = $path;
    }
}
