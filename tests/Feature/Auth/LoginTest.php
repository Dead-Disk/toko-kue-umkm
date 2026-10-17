<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_with_username_field(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Masuk');
        $response->assertSee('name="username"', false);
        $response->assertSee('name="password"', false);
    }

    public function test_seller_can_login_with_username_and_password(): void
    {
        $seller = User::factory()->create([
            'username' => 'penjual',
            'password' => 'rahasia123',
        ]);

        $response = $this->post(route('login.store'), [
            'username' => 'penjual',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($seller);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'username' => 'penjual',
            'password' => 'rahasia123',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'username' => 'penjual',
            'password' => 'salah',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_requires_username_and_password(): void
    {
        $response = $this->post(route('login.store'), []);

        $response->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login_when_opening_admin(): void
    {
        $response = $this->get(route('admin.produk.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_seller_can_logout(): void
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
