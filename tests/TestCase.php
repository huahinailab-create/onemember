<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Bare-path helpers (e.g. $this->get('/dashboard')) resolve to APP_URL,
    // which phpunit.xml sets to http://app.onemember.co so they hit the app
    // domain group. Corporate domain tests use full http://onemember.co/ URLs.
}
