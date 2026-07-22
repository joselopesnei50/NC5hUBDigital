<?php

namespace App\Mail;

use App\Models\Contrato;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContratoDisponivelMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contrato $contrato;
    public string $painelUrl;

    public function __construct(Contrato $contrato)
    {
        $this->contrato = $contrato;
        $this->painelUrl = route('customer.contracts');
    }

    public function build()
    {
        return $this->subject('Novo contrato disponível para assinatura — NC5 Hub')
                    ->view('emails.contrato_disponivel');
    }
}
