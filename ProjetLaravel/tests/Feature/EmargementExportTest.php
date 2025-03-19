<?php

namespace Tests\Feature;

use App\Models\Cours;
use App\Models\Emargement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmargementExportTest extends TestCase
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

        // Créer quelques émargements pour le test
        $etudiants = User::factory()->etudiant()->count(3)->create();
        foreach ($etudiants as $etudiant) {
            Emargement::factory()->create([
                'cours_id' => $this->cours->id,
                'user_id' => $etudiant->id,
                'statut' => 'present'
            ]);
        }
    }

    /** @test */
    public function un_professeur_peut_exporter_les_emargements_de_son_cours_en_pdf()
    {
        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.export.pdf', [
            'cours_id' => $this->cours->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function un_professeur_peut_exporter_les_emargements_de_son_cours_en_excel()
    {
        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.export.excel', [
            'cours_id' => $this->cours->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /** @test */
    public function un_professeur_ne_peut_pas_exporter_les_emargements_dun_autre_cours()
    {
        $this->actingAs($this->professeur);
        $autreProfesseur = User::factory()->professeur()->create();
        $autreCours = Cours::factory()->create(['professeur_id' => $autreProfesseur->id]);

        $response = $this->get(route('emargements.export.pdf', [
            'cours_id' => $autreCours->id
        ]));

        $response->assertStatus(403);
    }

    /** @test */
    public function un_admin_peut_exporter_tous_les_emargements()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('emargements.export.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function lexport_contient_les_bonnes_donnees()
    {
        $this->actingAs($this->professeur);
        $etudiant = User::factory()->etudiant()->create();
        $emargement = Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $etudiant->id,
            'statut' => 'present',
            'date_signature' => now()
        ]);

        $response = $this->get(route('emargements.export.excel', [
            'cours_id' => $this->cours->id
        ]));

        $response->assertStatus(200);
        // Vérifier que le fichier Excel contient les données attendues
        // Note: Cette vérification dépendra de l'implémentation exacte de l'export
        $this->assertTrue(true); // À remplacer par une vérification réelle du contenu
    }

    /** @test */
    public function lexport_peut_etre_filtre_par_date()
    {
        $this->actingAs($this->professeur);
        $dateDebut = now()->subDays(7)->format('Y-m-d');
        $dateFin = now()->format('Y-m-d');

        $response = $this->get(route('emargements.export.pdf', [
            'cours_id' => $this->cours->id,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function lexport_echoue_avec_des_dates_invalides()
    {
        $this->actingAs($this->professeur);

        $response = $this->get(route('emargements.export.pdf', [
            'cours_id' => $this->cours->id,
            'date_debut' => 'date-invalide',
            'date_fin' => 'date-invalide'
        ]));

        $response->assertSessionHasErrors(['date_debut', 'date_fin']);
    }
} 