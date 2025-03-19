<?php

namespace Tests\Unit;

use App\Models\Cours;
use App\Models\Emargement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmargementTest extends TestCase
{
    use RefreshDatabase;

    protected $cours;
    protected $etudiant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->etudiant = User::factory()->etudiant()->create();
        $professeur = User::factory()->professeur()->create();
        $this->cours = Cours::factory()->create([
            'professeur_id' => $professeur->id
        ]);
    }

    /** @test */
    public function un_emargement_appartient_a_un_cours()
    {
        $emargement = Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $this->assertInstanceOf(Cours::class, $emargement->cours);
        $this->assertEquals($this->cours->id, $emargement->cours->id);
    }

    /** @test */
    public function un_emargement_appartient_a_un_etudiant()
    {
        $emargement = Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $this->assertInstanceOf(User::class, $emargement->user);
        $this->assertEquals($this->etudiant->id, $emargement->user->id);
    }

    /** @test */
    public function un_emargement_peut_etre_marque_comme_present()
    {
        $emargement = Emargement::factory()->present()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $this->assertTrue($emargement->isPresent());
        $this->assertFalse($emargement->isAbsent());
        $this->assertFalse($emargement->isRetard());
        $this->assertNotNull($emargement->date_signature);
    }

    /** @test */
    public function un_emargement_peut_etre_marque_comme_absent()
    {
        $emargement = Emargement::factory()->absent()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $this->assertTrue($emargement->isAbsent());
        $this->assertFalse($emargement->isPresent());
        $this->assertFalse($emargement->isRetard());
        $this->assertNull($emargement->date_signature);
    }

    /** @test */
    public function un_emargement_peut_etre_marque_comme_retard()
    {
        $emargement = Emargement::factory()->retard()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $this->assertTrue($emargement->isRetard());
        $this->assertFalse($emargement->isPresent());
        $this->assertFalse($emargement->isAbsent());
        $this->assertNotNull($emargement->date_signature);
    }

    /** @test */
    public function un_etudiant_ne_peut_pas_avoir_plusieurs_emargements_pour_le_meme_cours()
    {
        Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);
    }

    /** @test */
    public function un_emargement_peut_avoir_un_commentaire()
    {
        $commentaire = 'Retard justifié par un problème de transport';
        $emargement = Emargement::factory()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'commentaire' => $commentaire
        ]);

        $this->assertEquals($commentaire, $emargement->commentaire);
    }

    /** @test */
    public function la_date_de_signature_est_automatiquement_convertie_en_instance_carbon()
    {
        $emargement = Emargement::factory()->present()->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $emargement->date_signature);
    }
} 