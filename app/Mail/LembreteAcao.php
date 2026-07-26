<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LembreteAcao extends Mailable
{
    use Queueable, SerializesModels;

    public $assunto;
    public $mensagem;
    public $linkAcao;
    public $textoBotao;

    /**
     * Create a new message instance.
     */
    public function __construct($assunto, $mensagem, $linkAcao = null, $textoBotao = 'Acessar Área do Cliente')
    {
        $this->assunto = $assunto;
        $this->mensagem = $mensagem;
        $this->linkAcao = $linkAcao;
        $this->textoBotao = $textoBotao;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->assunto,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lembrete-acao',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
