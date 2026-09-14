<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class BruceAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public Cliente $cliente;
    public Collection $alertas;
    public string $linkPainel;

    /**
     * @param Cliente $cliente empresa que recebe (o email vai para o user vinculado)
     * @param Collection $alertas coleção de AgentAlert críticos criados nesta rodada
     */
    public function __construct(Cliente $cliente, Collection $alertas)
    {
        $this->cliente = $cliente;
        $this->alertas = $alertas;
        $this->linkPainel = route('customer.alertas.index');
    }

    public function envelope(): Envelope
    {
        $qtd = $this->alertas->count();
        $subject = $qtd === 1
            ? '[BruceIA · Crítico] ' . $this->alertas->first()->titulo
            : "[BruceIA · Crítico] {$qtd} pontos precisam da sua atenção agora";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.bruce-alertas');
    }

    public function attachments(): array
    {
        return [];
    }
}
