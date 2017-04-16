<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HelperTest extends TestCase
{
    public function testSetActiveFilter()
    {
        request()->merge(['filter' => 'all']);
        $this->assertEquals('active', set_active_filter('all'));

        request()->merge(['filter' => str_random()]);
        $this->assertNull(set_active_filter('all', ['colleague', 'banned']));

        request()->merge(['filter' => 'colleague']);
        $this->assertEquals('', set_active_filter('all', ['colleague', 'banned']));
    }
}
