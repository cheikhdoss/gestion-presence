<?php

namespace App\Services;

use App\Models\Cours;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function generateEmargementsPdf(Cours $cours)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        
        $html = view('exports.emargements-pdf', [
            'cours' => $cours,
            'emargements' => $cours->emargements()->with('user')->get()
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->output();
    }
} 