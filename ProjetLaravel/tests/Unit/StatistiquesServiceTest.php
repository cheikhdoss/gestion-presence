<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cours;
use App\Models\Emargement;
use App\Services\StatistiquesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class StatistiquesServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $cours;
    protected $etudiant;
    protected $professeur;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->service = new StatistiquesService();

        // Création des données de test
        $this->professeur = User::factory()->create(['role' => 'professeur']);
        $this->etudiant = User::factory()->create(['role' => 'etudiant']);
        $this->cours = Cours::factory()->create(['professeur_id' => $this->professeur->id]);

        // Création des émargements
        Emargement::factory()->count(5)->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'present'
        ]);

        Emargement::factory()->count(3)->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'absent'
        ]);

        Emargement::factory()->count(2)->create([
            'cours_id' => $this->cours->id,
            'user_id' => $this->etudiant->id,
            'statut' => 'retard'
        ]);
    }

    public function test_get_statistiques_globales()
    {
        Cache::flush();

        $stats = $this->service->getStatistiquesGlobales();

        $this->assertEquals(10, $stats['total_emargements']);
        $this->assertEquals(5, $stats['total_presents']);
        $this->assertEquals(3, $stats['total_absents']);
        $this->assertEquals(2, $stats['total_retards']);
    }

    public function test_get_statistiques_cours()
    {
        Cache::flush();

        $stats = $this->service->getStatistiquesCours($this->cours->id);

        $this->assertEquals(10, $stats['total']);
        $this->assertEquals(5, $stats['presents']);
        $this->assertEquals(3, $stats['absents']);
        $this->assertEquals(2, $stats['retards']);
        $this->assertEquals(50, $stats['taux_presence']);
    }

    public function test_get_statistiques_etudiant()
    {
        Cache::flush();

        $stats = $this->service->getStatistiquesEtudiant($this->etudiant->id);

        $this->assertEquals(10, $stats['total']);
        $this->assertEquals(5, $stats['presents']);
        $this->assertEquals(3, $stats['absents']);
        $this->assertEquals(2, $stats['retards']);
        $this->assertEquals(50, $stats['taux_presence']);
    }

    public function test_get_tendances()
    {
        Cache::flush();

        $tendances = $this->service->getTendances();

        $this->assertIsArray($tendances);
        $this->assertCount(31, $tendances); // 30 jours + aujourd'hui
        
        // Vérification de la structure des données
        $today = date('Y-m-d');
        $this->assertArrayHasKey($today, $tendances);
        $this->assertArrayHasKey('presents', $tendances[$today]);
        $this->assertArrayHasKey('absents', $tendances[$today]);
        $this->assertArrayHasKey('retards', $tendances[$today]);
    }

    public function test_cache_invalidation()
    {
        // Test du cache global
        Cache::shouldReceive('forget')->with('statistiques.globales')->once();
        Cache::shouldReceive('forget')->with('statistiques.tendances')->once();
        $this->service->invalidateCache();

        // Test du cache par cours
        Cache::shouldReceive('forget')->with('statistiques.cours.1')->once();
        Cache::shouldReceive('forget')->with('statistiques.globales')->once();
        Cache::shouldReceive('forget')->with('statistiques.tendances')->once();
        $this->service->invalidateCache('cours', 1);

        // Test du cache par étudiant
        Cache::shouldReceive('forget')->with('statistiques.etudiant.1')->once();
        Cache::shouldReceive('forget')->with('statistiques.globales')->once();
        Cache::shouldReceive('forget')->with('statistiques.tendances')->once();
        $this->service->invalidateCache('etudiant', 1);
    }
} 