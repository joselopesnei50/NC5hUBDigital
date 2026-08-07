<?php

namespace App\Mail;

use App\Models\Material;
use App\Models\MaterialReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaterialRespostaAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public Material $material;
    public MaterialReply $reply;
    public string $painelUrl;

    public function __construct(Material $material, MaterialReply $reply)
    {
        $this->material = $material;
        $this->reply = $reply;
        $this->painelUrl = route('customer.materiais');
    }

    public function build()
    {
        return $this->subject('Atualização no seu material — NC5 Hub Digital')
                    ->view('emails.material_resposta_admin');
    }
}
