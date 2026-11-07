<?php

namespace App\Mail;

use App\Models\TermoImagem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TermoImagemMail extends Mailable
{
    use Queueable, SerializesModels;

    public TermoImagem $termo;
    public string $signUrl;

    public function __construct(TermoImagem $termo)
    {
        $this->termo = $termo->loadMissing('cliente');
        $this->signUrl = $termo->publicSignUrl();
    }

    public function build()
    {
        return $this->subject('Termo de Autorização de Uso de Imagem e Voz — NC5 Hub Digital')
                    ->view('emails.termo_imagem');
    }
}
