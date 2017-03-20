<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GithubLogin extends TestCase
{

    use DatabaseMigrations;


    public function testMe()
    {
        $this->assertTrue(true);
    }

}
