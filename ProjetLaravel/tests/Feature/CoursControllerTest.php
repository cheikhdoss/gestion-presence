<?php

namespace Tests\Feature;

use App\Models\Cours;
use App\Models\Salle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CoursControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $professeur;
    protected $etudiant;
    protected $salle;

    protected function setUp(): void
    {
        parent::setUp();

        // Création des utilisateurs de test
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);

        $this->professeur = User::factory()->create([
            'role' => 'professeur',
            'email' => 'professeur@test.com'
        ]);

        $this->etudiant = User::factory()->create([
            'role' => 'etudiant',
            'email' => 'etudiant@test.com'
        ]);

        // Création d'une salle de test
        $this->salle = Salle::factory()->create();
    }

    /** @test */
    public function un_utilisateur_non_authentifie_ne_peut_pas_acceder_a_la_liste_des_cours()
    {
        $response = $this->get(route('cours.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function un_utilisateur_authentifie_peut_voir_la_liste_des_cours()
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('cours.index'));
        $response->assertStatus(200);
        $response->assertViewIs('cours.index');
    }

    /** @test */
    public function les_filtres_de_recherche_fonctionnent_correctement()
    {
        $this->actingAs($this->admin);

        $cours1 = Cours::factory()->create([
            'matiere' => 'Mathématiques',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => now()
        ]);

        $cours2 = Cours::factory()->create([
            'matiere' => 'Physique',
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id,
            'date_heure' => now()->addDay()
        ]);

        // Test du filtre par matière
        $response = $this->get(route('cours.index', ['search' => 'Math']));
        $response->assertSee('Mathématiques');
        $response->assertDontSee('Physique');

        // Test du filtre par professeur
        $response = $this->get(route('cours.index', ['professeur' => $this->professeur->id]));
        $response->assertSee('Mathématiques');
        $response->assertSee('Physique');

        // Test du filtre par date
        $response = $this->get(route('cours.index', ['date' => now()->format('Y-m-d')]));
        $response->assertSee('Mathématiques');
        $response->assertDontSee('Physique');
    }

    /** @test */
    public function un_admin_peut_creer_un_cours()
    {
        $this->actingAs($this->admin);

        $coursData = [
            'matiere' => 'Nouveau cours',
            'description' => 'Description du cours',
            'date_heure' => now()->addDay()->format('Y-m-d H:i:s'),
            'salle_id' => $this->salle->id,
            'professeur_id' => $this->professeur->id
        ];

        $response = $this->post(route('cours.store'), $coursData);
        
        $response->assertRedirect(route('cours.index'));
        $this->assertDatabaseHas('cours', [
            'matiere' => 'Nouveau cours',
            'description' => 'Description du cours'
        ]);
    }

    /** @test */
    public function un_admin_peut_modifier_un_cours()
    {
        $this->actingAs($this->admin);

        $cours = Cours::factory()->create([
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id
        ]);

        $updateData = [
            'matiere' => 'Cours modifié',
            'description' => 'Nouvelle description',
            'date_heure' => now()->addDay()->format('Y-m-d H:i:s'),
            'salle_id' => $this->salle->id,
            'professeur_id' => $this->professeur->id
        ];

        $response = $this->put(route('cours.update', $cours), $updateData);
        
        $response->assertRedirect(route('cours.index'));
        $this->assertDatabaseHas('cours', [
            'id' => $cours->id,
            'matiere' => 'Cours modifié',
            'description' => 'Nouvelle description'
        ]);
    }

    /** @test */
    public function un_admin_peut_supprimer_un_cours()
    {
        $this->actingAs($this->admin);

        $cours = Cours::factory()->create([
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id
        ]);

        $response = $this->delete(route('cours.destroy', $cours));
        
        $response->assertRedirect(route('cours.index'));
        $this->assertDatabaseMissing('cours', ['id' => $cours->id]);
    }

    /** @test */
    public function les_donnees_de_creation_de_cours_sont_validees()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('cours.store'), []);
        
        $response->assertSessionHasErrors(['matiere', 'date_heure', 'salle_id', 'professeur_id']);
    }

    /** @test */
    public function un_professeur_peut_exporter_les_emargements_de_son_cours()
    {
        $this->actingAs($this->professeur);

        $cours = Cours::factory()->create([
            'professeur_id' => $this->professeur->id,
            'salle_id' => $this->salle->id
        ]);

        // Test export Excel
        $response = $this->get(route('cours.export', ['cours' => $cours, 'format' => 'excel']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Test export PDF
        $response = $this->get(route('cours.export', ['cours' => $cours, 'format' => 'pdf']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function un_professeur_ne_peut_pas_exporter_les_emargements_dun_autre_professeur()
    {
        $this->actingAs($this->professeur);

        $autreProfesseur = User::factory()->create(['role' => 'professeur']);
        $cours = Cours::factory()->create([
            'professeur_id' => $autreProfesseur->id,
            'salle_id' => $this->salle->id
        ]);

        $response = $this->get(route('cours.export', ['cours' => $cours, 'format' => 'excel']));
        $response->assertStatus(403);
    }
} 