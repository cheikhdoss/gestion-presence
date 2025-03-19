<?php

namespace Tests\Feature;

use App\Models\Cours;
use App\Models\Salle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $gestionnaire;
    private User $professeur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
        $this->professeur = User::factory()->create(['role' => 'professeur']);
    }

    public function test_admin_can_create_salle()
    {
        $response = $this->actingAs($this->admin)
            ->post('/salles', [
                'libelle' => 'Nouvelle Salle',
                'capacite' => 30,
                'equipements' => 'Projecteur, Tableau',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('salles', [
            'libelle' => 'Nouvelle Salle',
            'capacite' => 30,
        ]);
    }

    public function test_gestionnaire_can_create_salle()
    {
        $response = $this->actingAs($this->gestionnaire)
            ->post('/salles', [
                'libelle' => 'Salle Test',
                'capacite' => 25,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('salles', [
            'libelle' => 'Salle Test',
        ]);
    }

    public function test_professeur_cannot_create_salle()
    {
        $response = $this->actingAs($this->professeur)
            ->post('/salles', [
                'libelle' => 'Salle Non Autorisée',
                'capacite' => 20,
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('salles', [
            'libelle' => 'Salle Non Autorisée',
        ]);
    }

    public function test_can_view_salle_details()
    {
        $salle = Salle::create([
            'libelle' => 'Salle de Test',
            'capacite' => 30,
            'equipements' => 'Projecteur',
        ]);

        $response = $this->actingAs($this->professeur)
            ->get("/salles/{$salle->id}");

        $response->assertStatus(200);
        $response->assertSee('Salle de Test');
        $response->assertSee('Projecteur');
    }

    public function test_admin_can_update_salle()
    {
        $salle = Salle::create([
            'libelle' => 'Ancienne Salle',
            'capacite' => 20,
        ]);

        $response = $this->actingAs($this->admin)
            ->put("/salles/{$salle->id}", [
                'libelle' => 'Salle Mise à Jour',
                'capacite' => 25,
                'equipements' => 'Nouveau Projecteur',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('salles', [
            'id' => $salle->id,
            'libelle' => 'Salle Mise à Jour',
            'capacite' => 25,
        ]);
    }

    public function test_cannot_delete_salle_with_future_cours()
    {
        $salle = Salle::create([
            'libelle' => 'Salle à Supprimer',
            'capacite' => 30,
        ]);

        // Crée un cours futur dans cette salle
        Cours::create([
            'matiere' => 'Test',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $salle->id,
            'date_heure' => Carbon::now()->addDays(2),
        ]);

        $response = $this->actingAs($this->admin)
            ->delete("/salles/{$salle->id}");

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('salles', [
            'id' => $salle->id,
        ]);
    }

    public function test_can_delete_salle_without_cours()
    {
        $salle = Salle::create([
            'libelle' => 'Salle Vide',
            'capacite' => 30,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete("/salles/{$salle->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('salles', [
            'id' => $salle->id,
        ]);
    }

    public function test_can_check_salle_availability()
    {
        $salle = Salle::create([
            'libelle' => 'Salle Test',
            'capacite' => 30,
        ]);

        $dateHeure = Carbon::now()->addDay()->setHour(14)->setMinute(0);

        $response = $this->actingAs($this->professeur)
            ->get("/salles/{$salle->id}/disponibilite", [
                'date' => $dateHeure->toDateString(),
                'heure' => $dateHeure->format('H:i'),
            ]);

        $response->assertStatus(200);
        $response->assertJson(['disponible' => true]);
    }
} 