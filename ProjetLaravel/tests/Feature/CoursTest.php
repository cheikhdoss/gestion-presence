<?php

namespace Tests\Feature;

use App\Models\Cours;
use App\Models\Salle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $professeur;
    private Salle $salle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->professeur = User::factory()->create(['role' => 'professeur']);
        $this->salle = Salle::create(['libelle' => 'Salle Test']);
    }

    public function test_admin_can_create_cours()
    {
        $response = $this->actingAs($this->admin)->post('/cours', [
            'matiere' => 'Mathématiques',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date' => '2024-03-20',
            'heure' => '14:00',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cours', [
            'matiere' => 'Mathématiques',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
        ]);
    }

    public function test_cannot_create_cours_with_conflicting_schedule()
    {
        // Crée un premier cours
        Cours::create([
            'matiere' => 'Physique',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => '2024-03-20 14:00:00',
        ]);

        // Tente de créer un cours au même moment
        $response = $this->actingAs($this->admin)->post('/cours', [
            'matiere' => 'Chimie',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date' => '2024-03-20',
            'heure' => '14:00',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_professeur_can_view_own_cours()
    {
        $cours = Cours::create([
            'matiere' => 'Informatique',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => '2024-03-20 14:00:00',
        ]);

        $response = $this->actingAs($this->professeur)->get('/mes-cours');

        $response->assertStatus(200);
        $response->assertSee('Informatique');
    }

    public function test_can_update_cours()
    {
        $cours = Cours::create([
            'matiere' => 'Anglais',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => '2024-03-20 14:00:00',
        ]);

        $response = $this->actingAs($this->admin)->put("/cours/{$cours->id}", [
            'matiere' => 'Anglais avancé',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date' => '2024-03-20',
            'heure' => '15:00',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cours', [
            'id' => $cours->id,
            'matiere' => 'Anglais avancé',
        ]);
    }

    public function test_cannot_delete_past_cours()
    {
        $cours = Cours::create([
            'matiere' => 'Histoire',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => Carbon::yesterday(),
        ]);

        $response = $this->actingAs($this->admin)->delete("/cours/{$cours->id}");

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('cours', ['id' => $cours->id]);
    }

    public function test_can_delete_future_cours()
    {
        $cours = Cours::create([
            'matiere' => 'Géographie',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->admin)->delete("/cours/{$cours->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('cours', ['id' => $cours->id]);
    }
} 