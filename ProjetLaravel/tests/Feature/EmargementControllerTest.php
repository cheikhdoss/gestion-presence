<?php

namespace Tests\Feature;

use App\Models\Cours;
use App\Models\Emargement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmargementControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    }

    /** @test */
    public function un_etudiant_peut_signer_son_emargement()
    {
        $this->actingAs($this->etudiant);

        $response = $this->post(route('emargements.store'), [
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'present'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('emargements', [
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'present'
        ]);
    }

    /** @test */
    public function un_etudiant_ne_peut_pas_signer_pour_un_autre_etudiant()
    {
        $this->actingAs($this->etudiant);
        $autreEtudiant = User::factory()->etudiant()->create();

        $response = $this->post(route('emargements.store'), [
            'cours_id' => $this->cours->id,
            'user_id' => $autreEtudiant->id,
            'statut' => 'present'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function un_professeur_peut_marquer_un_etudiant_comme_absent()
    {
        $this->actingAs($this->professeur);

        $response = $this->post(route('emargements.store'), [
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'absent',
            'commentaire' => 'Absence non justifiée'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('emargements', [
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'absent',
            'commentaire' => 'Absence non justifiée'
        ]);
    }

    /** @test */
    public function un_professeur_ne_peut_pas_modifier_les_emargements_dun_autre_cours()
    {
        $this->actingAs($this->professeur);
        $autreProfesseur = User::factory()->professeur()->create();
        $autreCours = Cours::factory()->create(['professeur_id' => $autreProfesseur->id]);

        $response = $this->post(route('emargements.store'), [
            'cours_id' => $autreCours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'absent'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function un_admin_peut_modifier_tous_les_emargements()
    {
        $this->actingAs($this->admin);
        $emargement = Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'absent'
        ]);

        $response = $this->put(route('emargements.update', $emargement), [
            'statut' => 'present',
            'commentaire' => 'Modifié par admin'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('emargements', [
            'id' => $emargement->id,
            'statut' => 'present',
            'commentaire' => 'Modifié par admin'
        ]);
    }

    /** @test */
    public function les_donnees_demargement_sont_validees()
    {
        $this->actingAs($this->professeur);

        $response = $this->post(route('emargements.store'), []);
        
        $response->assertSessionHasErrors(['cours_id', 'user_id', 'statut']);
    }

    /** @test */
    public function le_statut_doit_etre_valide()
    {
        $this->actingAs($this->professeur);

        $response = $this->post(route('emargements.store'), [
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'statut_invalide'
        ]);

        $response->assertSessionHasErrors('statut');
    }

    /** @test */
    public function un_emargement_peut_etre_supprime_par_un_admin()
    {
        $this->actingAs($this->admin);
        $emargement = Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $response = $this->delete(route('emargements.destroy', $emargement));

        $response->assertRedirect();
        $this->assertDatabaseMissing('emargements', ['id' => $emargement->id]);
    }

    /** @test */
    public function un_professeur_peut_voir_la_liste_des_emargements_de_son_cours()
    {
        $this->actingAs($this->professeur);
        $emargement = Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $response = $this->get(route('cours.show', $this->cours));

        $response->assertStatus(200);
        $response->assertSee($this->etudiant->nom);
        $response->assertSee($emargement->statut);
    }
} 