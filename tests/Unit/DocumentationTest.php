<?php

namespace Tests\Unit;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;
use Yap\Foundation\Documentation;

class DocumentationTest extends TestCase
{

    /**
     * @var Documentation $documentation
     */
    protected $documentation;

    /**
     * @var Config $config
     */
    protected $config;


    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->config = resolve(Config::class);
        $this->config->set('documentation.path', 'tests/Unit/Stubs/docs/');
        $this->documentation = resolve(Documentation::class);
    }


    public function testDocumentation()
    {
        $pages = [
            'administrator'       => 'Administrator',
            'integration-testing' => 'Integration Testing',
            'team-leader'         => 'Team Leader',
            'team-member'         => 'Team Member',
            'unit-testing'        => 'Unit Testing'
        ];

        $files = new Filesystem();

        $stub = $files->get(base_path('tests/Unit/Stubs/docs/compiled-stub.html'));

        foreach ($pages as $page => $title) {
            $compiled = str_replace('{{name}}', $title, $stub);
            $html = $this->documentation->get($page);
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

        $index = $this->documentation->getIndex();

        foreach ($prospects as $prospect) {
            $this->assertContains($prospect, $index);
        }
    }
}
