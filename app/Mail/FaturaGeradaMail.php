<?php

namespace App\Mail;

use App\Models\Fatura;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FaturaGeradaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fatura;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Fatura $fatura)
    {
        $this->fatura = $fatura;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Nova fatura disponível - NC5 Hub',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.fatura_gerada',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        $attachments = [];
        
        // Se houver anexo da fatura, poderia ser enviado aqui
        // Mas a lógica será apenas o link de visualização por segurança
        
        return $attachments;
    }
}
