<?php

namespace App\Mail;

use App\Models\ContratoPrestador;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContratoPrestadorMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContratoPrestador $contrato;
    public string $signUrl;

    public function __construct(ContratoPrestador $contrato)
    {
        $this->contrato = $contrato->loadMissing('prestador');
        $this->signUrl = $contrato->publicSignUrl();
    }

    public function build()
    {
        return $this->subject('Contrato de prestação de serviço — NC5 Hub Digital')
                    ->view('emails.contrato_prestador');
    }
}
