<?php

namespace App\Mail;

use App\Models\Material;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaterialPendenteMail extends Mailable
{
    use Queueable, SerializesModels;

    public Material $material;
    public string $painelUrl;

    public function __construct(Material $material)
    {
        $this->material = $material;
        $this->painelUrl = route('customer.materiais');
    }

    public function build()
    {
        return $this->subject('Novo Material Disponível para Aprovação — NC5 Hub Digital')
                    ->view('emails.material_pendente');
    }
}
