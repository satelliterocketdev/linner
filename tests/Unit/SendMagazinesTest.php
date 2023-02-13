<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\CreatesApplication;

class SendMagazinesTest extends BaseTestCase
{
    use CreatesApplication;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSendMagazines()
    {
        $exitCode = \Artisan::call('command:sendMagazines');
        $this->assertTrue(true);
    }
}
