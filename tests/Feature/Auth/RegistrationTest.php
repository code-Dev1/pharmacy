<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_registration_route_is_disabled(): void
    {
        $response = $this->get('/register');

        $response->assertNotFound();
    }

    public function test_root_page_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }
}
