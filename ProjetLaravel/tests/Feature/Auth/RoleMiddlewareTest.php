<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/users');

        $response->assertStatus(200);
    }

    public function test_gestionnaire_cannot_access_admin_routes()
    {
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire']);

        $response = $this->actingAs($gestionnaire)->get('/users');

        $response->assertStatus(403);
    }

    public function test_professeur_cannot_access_admin_routes()
    {
        $professeur = User::factory()->create(['role' => 'professeur']);

        $response = $this->actingAs($professeur)->get('/users');

        $response->assertStatus(403);
    }

    public function test_gestionnaire_can_access_salles()
    {
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire']);

        $response = $this->actingAs($gestionnaire)->get('/salles');

        $response->assertStatus(200);
    }

    public function test_professeur_can_access_mes_cours()
    {
        $professeur = User::factory()->create(['role' => 'professeur']);

        $response = $this->actingAs($professeur)->get('/mes-cours');

        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
} 