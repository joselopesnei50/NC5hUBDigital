<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        // Filtros básicos opcionais (busca por nome/email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo de análise
        if ($request->filled('tipo')) {
            $query->where('tipo_analise', $request->tipo);
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.leads.index', compact('leads'));
    }

    public function export()
    {
        $leads = Lead::orderBy('created_at', 'desc')->get();

        $filename = "leads_nc5_" . date('Y-m-d_H-i') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Bom para o Excel não dar problema de acentuação
        $callback = function() use($leads) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            // Cabeçalhos
            fputcsv($file, [
                'Data',
                'Nome',
                'E-mail',
                'WhatsApp',
                'Tipo de Análise',
                'Seguidores IG',
                'Score SEO',
                'Score Performance',
                'Dor Principal',
                'URL/Instagram'
            ], ';');

            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->created_at->format('d/m/Y H:i'),
                    $lead->nome,
                    $lead->email,
                    $lead->whatsapp,
                    $lead->tipo_analise,
                    $lead->ig_followers ?? '0',
                    $lead->seo_score ?? '0',
                    $lead->performance_score ?? '0',
                    $lead->dor_site ?? $lead->dor_social ?? '-',
                    $lead->url_site ?? $lead->url_social ?? '-'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
