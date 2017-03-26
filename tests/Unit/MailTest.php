<?php

namespace Tests\Unit;

use Illuminate\Contracts\Queue\ShouldQueue;
use Tests\TestCase;

class MailTest extends TestCase
{

    public function testMailsAreQueued()
    {
        $files = \File::allFiles(app_path('Mail'));

        foreach ($files as $file) {
            $mailable = resolve('Yap\\Mail\\'.basename($file->getBasename('.php')));
            $this->assertArrayHasKey(ShouldQueue::class, class_implements($mailable), 'Mailable ['.get_class($mailable).'] does not implement ShouldQueue interface.');
            $this->assertTrue(property_exists($mailable, 'queue'), 'Mailable ['.get_class($mailable).'] does not have queue property.');
            $this->assertEquals('emails', $mailable->queue, 'Mailable ['.get_class($mailable).'] queue must be \'email\'.');
        }
    }
}
