<?php

namespace Tests\Feature;

use App\Models\Cours;
use App\Models\Emargement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmargementListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $professeur;
    protected $cours;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->professeur = User::factory()->professeur()->create();
        
        $this->cours = Cours::factory()->create([
            'professeur_id' => $this->professeur->id
        ]);

        // Créer 30 émargements pour tester la pagination
        $etudiants = User::factory()->etudiant()->count(10)->create();
        foreach ($etudiants as $etudiant) {
            Emargement::factory()->count(3)->create([
                'cours_id' => $this->cours->id,
                'user_id' => $etudiant->id,
                'statut' => ['present', 'absent', 'retard'][rand(0, 2)]
            ]);
        }
    }

    /** @test */
    public function la_liste_des_emargements_est_paginee()
    {
        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.index', [
            'cours_id' => $this->cours->id
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('emargements');
        $this->assertEquals(15, $response['emargements']->perPage());
    }

    /** @test */
    public function les_emargements_peuvent_etre_filtres_par_statut()
    {
        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.index', [
            'cours_id' => $this->cours->id,
            'statut' => 'present'
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('emargements');
        $emargements = $response['emargements'];
        
        foreach ($emargements as $emargement) {
            $this->assertEquals('present', $emargement->statut);
        }
    }

    /** @test */
    public function les_emargements_peuvent_etre_filtres_par_date()
    {
        $this->actingAs($this->professeur);
        $date = now()->format('Y-m-d');

        $response = $this->get(route('emargements.index', [
            'cours_id' => $this->cours->id,
            'date' => $date
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('emargements');
        $emargements = $response['emargements'];
        
        foreach ($emargements as $emargement) {
            $this->assertEquals($date, $emargement->created_at->format('Y-m-d'));
        }
    }

    /** @test */
    public function les_emargements_peuvent_etre_filtres_par_etudiant()
    {
        $this->actingAs($this->professeur);
        $etudiant = User::factory()->etudiant()->create();
        Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $etudiant->id
        ]);

        $response = $this->get(route('emargements.index', [
            'cours_id' => $this->cours->id,
            'etudiant_id' => $etudiant->id
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('emargements');
        $emargements = $response['emargements'];
        
        foreach ($emargements as $emargement) {
            $this->assertEquals($etudiant->id, $emargement->user_id);
        }
    }

    /** @test */
    public function les_emargements_peuvent_etre_tries()
    {
        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.index', [
            'cours_id' => $this->cours->id,
            'sort' => 'date_signature',
            'direction' => 'desc'
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('emargements');
        
        $emargements = $response['emargements'];
        $this->assertEquals('desc', $emargements->getCollection()->implode('date_signature', ','));
    }

    /** @test */
    public function la_recherche_demargements_fonctionne()
    {
        $this->actingAs($this->professeur);
        $etudiant = User::factory()->etudiant()->create([
            'nom' => 'Dupont',
            'prenom' => 'Jean'
        ]);
        Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $etudiant->id
        ]);

        $response = $this->get(route('emargements.index', [
            'cours_id' => $this->cours->id,
            'search' => 'Dupont'
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('emargements');
        $response->assertSee('Dupont');
    }

    /** @test */
    public function les_filtres_peuvent_etre_combines()
    {
        $this->actingAs($this->professeur);
        $date = now()->format('Y-m-d');

        $response = $this->get(route('emargements.index', [
            'cours_id' => $this->cours->id,
            'statut' => 'present',
            'date' => $date,
            'sort' => 'date_signature',
            'direction' => 'desc'
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('emargements');
        
        $emargements = $response['emargements'];
        foreach ($emargements as $emargement) {
            $this->assertEquals('present', $emargement->statut);
            $this->assertEquals($date, $emargement->created_at->format('Y-m-d'));
        }
    }

    /** @test */
    public function les_parametres_de_filtre_invalides_sont_ignores()
    {
        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.index', [
            'cours_id' => $this->cours->id,
            'statut' => 'statut_invalide',
            'sort' => 'champ_invalide'
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('emargements');
    }
} 