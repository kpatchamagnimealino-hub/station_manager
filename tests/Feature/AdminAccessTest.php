<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrateur',
            'email' => 'admin@stationmanager.test',
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Tableau de bord administrateur');
    }

    public function test_non_admin_user_is_blocked_from_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Utilisateur',
            'email' => 'user@stationmanager.test',
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_page(): void
    {
        $this->get('/admin')
            ->assertRedirect('/login');
    }

    public function test_login_page_shows_all_three_profiles(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Administrateur')
            ->assertSee('Responsable')
            ->assertSee('Directeur général');
    }

    public function test_admin_can_login(): void
    {
        User::factory()->create([
            'name' => 'Administrateur',
            'email' => 'admin-login@stationmanager.test',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $this->post('/login', [
            'role' => User::ROLE_ADMINISTRATOR,
            'email' => 'admin-login@stationmanager.test',
            'password' => 'password',
        ])
            ->assertRedirect('/admin');

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
    }

    public function test_account_cannot_login_using_another_profile(): void
    {
        User::factory()->create([
            'name' => 'Utilisateur',
            'email' => 'user-login@stationmanager.test',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $this->from('/login')
            ->post('/login', [
                'role' => User::ROLE_ADMINISTRATOR,
                'email' => 'user-login@stationmanager.test',
                'password' => 'password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_station_manager_can_login_and_is_limited_to_operations(): void
    {
        User::factory()->create([
            'name' => 'Responsable',
            'email' => 'manager-login@stationmanager.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STATION_MANAGER,
        ]);

        $this->withSession(['url.intended' => '/admin'])
            ->post('/login', [
                'role' => User::ROLE_STATION_MANAGER,
                'email' => 'manager-login@stationmanager.test',
                'password' => 'password',
            ])->assertRedirect('/responsable');

        $this->get('/responsable')
            ->assertOk()
            ->assertSee('Opérations quotidiennes')
            ->assertSee('Stocks par station')
            ->assertSee('Saisir une vente')
            ->assertDontSee('Vue direction générale');
        $this->get('/admin/sales')->assertOk();
        $this->get('/admin/supplies')->assertOk();
        $this->get('/admin/stations')->assertForbidden();
        $this->get('/admin/products')->assertForbidden();
        $this->get('/admin/reports')->assertForbidden();
        $this->get('/admin')->assertForbidden();
    }

    public function test_general_director_can_view_reports_but_not_manage_operations(): void
    {
        User::factory()->create([
            'name' => 'Directeur général',
            'email' => 'director-login@stationmanager.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_GENERAL_DIRECTOR,
        ]);

        $this->post('/login', [
            'role' => User::ROLE_GENERAL_DIRECTOR,
            'email' => 'director-login@stationmanager.test',
            'password' => 'password',
        ])->assertRedirect('/direction');

        $this->get('/direction')
            ->assertOk()
            ->assertSee('Vue direction générale')
            ->assertSee('Ventes du jour par station')
            ->assertDontSee('Saisir une vente');
        $this->get('/admin/reports')->assertOk();
        $this->get('/admin/stations')->assertForbidden();
        $this->get('/admin')->assertForbidden();
    }
}
