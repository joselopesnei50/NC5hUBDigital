<?php

namespace App\Mail;

use App\Models\Material;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaterialAvaliadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Material $material;
    public string $adminUrl;

    public function __construct(Material $material)
    {
        $this->material = $material;
        $this->adminUrl = route('admin.materiais.show', $material->id);
    }

    public function build()
    {
        $statusTexto = match($this->material->status_aprovacao) {
            'aprovado' => 'Aprovado ✓',
            'ajustes_solicitados' => 'Solicitação de Ajustes ⚠️',
            'reprovado' => 'Reprovado ❌',
            default => 'Avaliado',
        };

        return $this->subject("Material {$statusTexto}: {$this->material->titulo} — NC5 Hub")
                    ->view('emails.material_avaliado');
    }
}
