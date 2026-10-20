<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_staff_user(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Staf Toko',
            'username' => 'staf.toko',
            'email' => 'staf@tokokue.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'staff',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'staf.toko',
            'email' => 'staf@tokokue.test',
            'role' => 'staff',
        ]);

        $staf = User::where('username', 'staf.toko')->firstOrFail();
        $this->assertTrue(Hash::check('rahasia123', $staf->password));
    }

    public function test_store_validates_unique_email_short_password_and_role(): void
    {
        $admin = User::factory()->create();
        User::factory()->create(['email' => 'sudah@tokokue.test']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Staf Baru',
            'username' => 'staf.baru',
            'email' => 'sudah@tokokue.test',
            'password' => 'pendek',
            'password_confirmation' => 'pendek',
            'role' => 'pemilik',
        ]);

        $response->assertSessionHasErrors(['email', 'password', 'role']);
        $this->assertDatabaseMissing('users', ['username' => 'staf.baru']);
    }

    public function test_staff_gets_forbidden_on_user_management(): void
    {
        $staff = User::factory()->staff()->create();
        $other = User::factory()->create();

        $this->actingAs($staff)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.users.create'))->assertForbidden();
        $this->actingAs($staff)
            ->post(route('admin.users.store'), [
                'name' => 'Staf Palsu',
                'username' => 'staf.palsu',
                'email' => 'palsu@tokokue.test',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
                'role' => 'admin',
            ])
            ->assertForbidden();
        $this->actingAs($staff)->delete(route('admin.users.destroy', $other))->assertForbidden();
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasErrors('user');
        $this->assertModelExists($admin);
    }

    public function test_admin_can_update_user_and_reset_password(): void
    {
        $admin = User::factory()->create();
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $staff), [
            'name' => 'Nama Baru',
            'username' => $staff->username,
            'email' => $staff->email,
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $staff->refresh();
        $this->assertSame('Nama Baru', $staff->name);
        $this->assertSame('admin', $staff->role);
        $this->assertTrue(Hash::check('passwordbaru', $staff->password));
    }

    public function test_admin_can_delete_staff(): void
    {
        $admin = User::factory()->create();
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $staff));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertModelMissing($staff);
    }
}
