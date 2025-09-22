<?php

namespace jdavidbakr\MailTracker\Tests;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use jdavidbakr\MailTracker\MailTracker;

class ConversionCookieTest extends SetUpTest
{
    /**
     * @test
     */
    public function it_sets_conversion_cookie_when_enabled()
    {
        Config::set('mail-tracker.track_conversions', true);

        $tracker = MailTracker::sentEmailModel()->newQuery()->create([
            'hash' => Str::random(32),
        ]);

        $url = 'http://example.com/landing';

        $response = $this->get("/mail-tracker/n?l={$url}&h={$tracker->hash}");

        $response->assertRedirect($url);

        $response->assertCookie(
            config('mail-tracker.conversion_cookie_name'),
            $tracker->id
        );
    }

    /**
     * @test
     */
    public function it_does_not_set_cookie_when_disabled()
    {
        Config::set('mail-tracker.track_conversions', false);

        $tracker = MailTracker::sentEmailModel()->newQuery()->create([
            'hash' => Str::random(32),
        ]);

        $url = 'http://example.com/landing';

        $response = $this->get("/mail-tracker/n?l={$url}&h={$tracker->hash}");

        $response->assertRedirect($url);

        $response->assertCookieMissing(
            config('mail-tracker.conversion_cookie_name')
        );
    }
}
