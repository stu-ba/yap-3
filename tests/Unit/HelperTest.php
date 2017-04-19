<?php

namespace Tests\Unit;

use Tests\TestCase;

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
