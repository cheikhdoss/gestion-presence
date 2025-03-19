<?php

namespace App\Exports;

use App\Models\Emargement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class EmargementsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        return Emargement::query()
            ->with(['cours.professeur', 'cours.salle', 'user'])
            ->when($this->request->filled('cours_id'), function ($query) {
                $query->where('cours_id', $this->request->cours_id);
            })
            ->when($this->request->filled('statut'), function ($query) {
                $query->where('statut', $this->request->statut);
            })
            ->when($this->request->filled('date_debut'), function ($query) {
                $query->whereDate('date_signature', '>=', $this->request->date_debut);
            })
            ->when($this->request->filled('date_fin'), function ($query) {
                $query->whereDate('date_signature', '<=', $this->request->date_fin);
            })
            ->orderBy('date_signature', 'desc');
    }

    public function headings(): array
    {
        return [
            'Cours',
            'Professeur',
            'Salle',
            'Étudiant',
            'Statut',
            'Date de signature',
            'Commentaire'
        ];
    }

    public function map($emargement): array
    {
        return [
            $emargement->cours->matiere,
            $emargement->cours->professeur->name,
            $emargement->cours->salle->nom,
            $emargement->user->name,
            ucfirst($emargement->statut),
            $emargement->date_signature ? $emargement->date_signature->format('d/m/Y H:i') : '',
            $emargement->commentaire
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:G1' => ['fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'CCCCCC']]],
        ];
    }
} 