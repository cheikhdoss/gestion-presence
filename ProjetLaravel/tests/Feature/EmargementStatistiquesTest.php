<?php

namespace Tests\Feature;

use App\Models\Cours;
use App\Models\Emargement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmargementStatistiquesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $professeur;
    protected $etudiant;
    protected $cours;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->professeur = User::factory()->professeur()->create();
        $this->etudiant = User::factory()->etudiant()->create();
        
        $this->cours = Cours::factory()->create([
            'professeur_id' => $this->professeur->id
        ]);

        // Créer des émargements pour les tests
        Emargement::factory()->count(5)->present()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        Emargement::factory()->count(3)->absent()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        Emargement::factory()->count(2)->retard()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);
    }

    /** @test */
    public function un_admin_peut_voir_les_statistiques()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('emargements.statistiques'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $stats = $response['stats'];
        
        $this->assertEquals(10, $stats['total']);
        $this->assertEquals(5, $stats['presents']);
        $this->assertEquals(3, $stats['absents']);
        $this->assertEquals(2, $stats['retards']);
    }

    /** @test */
    public function un_professeur_peut_voir_les_statistiques_de_ses_cours()
    {
        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.statistiques'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response['stats'];
        $this->assertEquals(10, $stats['total']);
    }

    /** @test */
    public function un_professeur_ne_voit_pas_les_statistiques_des_autres_cours()
    {
        $autreProfesseur = User::factory()->professeur()->create();
        $autreCours = Cours::factory()->create(['professeur_id' => $autreProfesseur->id]);
        
        Emargement::factory()->count(5)->create([
            'cours_id' => $autreCours->id,
            'user_id' => $this->etudiant->id
        ]);

        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.statistiques'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response['stats'];
        $this->assertEquals(10, $stats['total']); // Seulement ses propres émargements
    }

    /** @test */
    public function un_etudiant_peut_voir_ses_statistiques()
    {
        $this->actingAs($this->etudiant);

        $response = $this->get(route('emargements.statistiques'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response['stats'];
        $this->assertEquals(10, $stats['total']);
    }

    /** @test */
    public function un_etudiant_ne_voit_pas_les_statistiques_des_autres()
    {
        $autreEtudiant = User::factory()->etudiant()->create();
        Emargement::factory()->count(5)->create([
            'cours_id' => $this->cours->id,
            'user_id' => $autreEtudiant->id
        ]);

        $this->actingAs($this->etudiant);

        $response = $this->get(route('emargements.statistiques'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response['stats'];
        $this->assertEquals(10, $stats['total']); // Seulement ses propres émargements
    }

    /** @test */
    public function les_statistiques_peuvent_etre_filtrees_par_date()
    {
        $this->actingAs($this->admin);

        $dateDebut = now()->subDays(7)->format('Y-m-d');
        $dateFin = now()->format('Y-m-d');

        $response = $this->get(route('emargements.statistiques', [
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    /** @test */
    public function les_statistiques_peuvent_etre_filtrees_par_cours()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('emargements.statistiques', [
            'cours_id' => $this->cours->id
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response['stats'];
        $this->assertEquals(10, $stats['total']);
    }

    /** @test */
    public function les_pourcentages_sont_calcules_correctement()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('emargements.statistiques'));

        $response->assertStatus(200);
        $stats = $response['stats'];
        
        $this->assertEquals(50, $stats['presents_percentage']);
        $this->assertEquals(30, $stats['absents_percentage']);
        $this->assertEquals(20, $stats['retards_percentage']);
    }
} 