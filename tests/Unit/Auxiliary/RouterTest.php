<?php

namespace Tests\Unit\Auxiliary;

use Tests\TestCase;

class RouterTest extends TestCase
{

    public function testRouter()
    {
        $this->withoutMiddleware();
        $expected = ['url' => route('api.router', [null])];
        $this->visitRoute('api.router', ['api.router']);

        $this->response->isOk();
        $this->assertEquals(json_encode($expected), $this->response->getContent());
    }
}
