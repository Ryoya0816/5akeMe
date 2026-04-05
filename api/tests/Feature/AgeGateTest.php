<?php

namespace Tests\Feature;

use Tests\TestCase;

class AgeGateTest extends TestCase
{
    public function test_top_redirects_to_age_check_without_verified_cookie(): void
    {
        $response = $this->get('/top');

        $response->assertRedirect(route('age.check'));
    }

    public function test_top_ok_with_age_verified_cookie(): void
    {
        $response = $this->withCookie('age_verified', '1')->get('/top');

        $response->assertOk();
    }

    public function test_security_headers_middleware_sets_csp(): void
    {
        $response = $this->get('/age-check');

        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }
}
