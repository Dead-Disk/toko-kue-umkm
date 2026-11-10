<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.profile.edit'))->assertRedirect(route('login'));
        $this->put(route('admin.profile.update'), ['name' => 'Test'])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_own_profile_page(): void
    {
        $admin = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => 'budi.admin',
            'email' => 'budi@tokokue.test',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.profile.edit'));

        $response->assertOk();
        $response->assertSee('Profil Akun');
        $response->assertSee('Budi Santoso');
        $response->assertSee('budi.admin');
        $response->assertSee('budi@tokokue.test');
        $response->assertSee('Administrator');
    }

    public function test_staff_can_view_own_profile_page(): void
    {
        $staff = User::factory()->staff()->create([
            'name' => 'Dewi Staff',
            'username' => 'dewi.staff',
            'email' => 'dewi@tokokue.test',
        ]);

        $response = $this->actingAs($staff)->get(route('admin.profile.edit'));

        $response->assertOk();
        $response->assertSee('Profil Akun');
        $response->assertSee('Dewi Staff');
        $response->assertSee('dewi.staff');
        $response->assertSee('Staf Toko');
    }

    public function test_user_can_update_profile_info(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Awal',
            'username' => 'username.awal',
            'email' => 'awal@tokokue.test',
        ]);

        $response = $this->actingAs($user)->put(route('admin.profile.update'), [
            'name' => 'Nama Baru',
            'username' => 'username.baru',
            'email' => 'baru@tokokue.test',
        ]);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('username.baru', $user->username);
        $this->assertSame('baru@tokokue.test', $user->email);
    }

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put(route('admin.profile.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'current_password' => 'password123',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('passwordbaru123', $user->password));
    }

    public function test_password_update_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put(route('admin.profile.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'current_password' => 'passwordsalah',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_update_fails_if_username_or_email_already_used(): void
    {
        User::factory()->create([
            'username' => 'user.lain',
            'email' => 'lain@tokokue.test',
        ]);

        $user = User::factory()->create([
            'username' => 'user.saya',
            'email' => 'saya@tokokue.test',
        ]);

        $response = $this->actingAs($user)->put(route('admin.profile.update'), [
            'name' => 'Saya Baru',
            'username' => 'user.lain',
            'email' => 'lain@tokokue.test',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
    }

    public function test_sidebar_contains_link_to_profile(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee(route('admin.profile.edit'));
    }
}
