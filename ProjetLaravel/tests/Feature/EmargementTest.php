<?php

namespace Tests\Feature;

use App\Models\Cours;
use App\Models\Emargement;
use App\Models\Salle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmargementTest extends TestCase
{
    use RefreshDatabase;

    private User $professeur;
    private Salle $salle;
    private Cours $cours;

    protected function setUp(): void
    {
        parent::setUp();

        $this->professeur = User::factory()->create(['role' => 'professeur']);
        $this->salle = Salle::create(['libelle' => 'Salle Test']);
        
        // Crée un cours qui a eu lieu il y a une heure
        $this->cours = Cours::create([
            'matiere' => 'Test',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => Carbon::now()->subHour(),
        ]);
    }

    public function test_professeur_can_sign_own_cours()
    {
        $response = $this->actingAs($this->professeur)
            ->post("/cours/{$this->cours->id}/emargement", [
                'commentaire' => 'Cours terminé',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('emargements', [
            'cours_id' => $this->cours->id,
            'commentaire' => 'Cours terminé',
        ]);
    }

    public function test_professeur_cannot_sign_other_cours()
    {
        $autreProfesseur = User::factory()->create(['role' => 'professeur']);
        
        $response = $this->actingAs($autreProfesseur)
            ->post("/cours/{$this->cours->id}/emargement", [
                'commentaire' => 'Cours terminé',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('emargements', [
            'cours_id' => $this->cours->id,
        ]);
    }

    public function test_cannot_sign_future_cours()
    {
        $coursFutur = Cours::create([
            'matiere' => 'Test Futur',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => Carbon::now()->addHour(),
        ]);

        $response = $this->actingAs($this->professeur)
            ->post("/cours/{$coursFutur->id}/emargement", [
                'commentaire' => 'Cours terminé',
            ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('emargements', [
            'cours_id' => $coursFutur->id,
        ]);
    }

    public function test_cannot_sign_cours_twice()
    {
        // Premier émargement
        Emargement::create([
            'cours_id' => $this->cours->id,
            'date_signature' => now(),
        ]);

        // Tentative de second émargement
        $response = $this->actingAs($this->professeur)
            ->post("/cours/{$this->cours->id}/emargement", [
                'commentaire' => 'Deuxième signature',
            ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('emargements', 1);
    }

    public function test_gestionnaire_can_view_all_emargements()
    {
        $gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        
        Emargement::create([
            'cours_id' => $this->cours->id,
            'date_signature' => now(),
            'commentaire' => 'Test',
        ]);

        $response = $this->actingAs($gestionnaire)->get('/emargements');

        $response->assertStatus(200);
        $response->assertSee('Test');
    }

    public function test_professeur_can_view_own_emargement()
    {
        Emargement::create([
            'cours_id' => $this->cours->id,
            'date_signature' => now(),
            'commentaire' => 'Test vue',
        ]);

        $response = $this->actingAs($this->professeur)
            ->get("/cours/{$this->cours->id}/emargement");

        $response->assertStatus(200);
        $response->assertSee('Test vue');
    }
} 