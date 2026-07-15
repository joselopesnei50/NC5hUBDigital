<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

/**
 * Envia e-mail pela API HTTP do Brevo (v3/smtp/email).
 *
 * Funciona apenas com API key (xkeysib-...). Não precisa de login SMTP —
 * é o modo padrão do Vivensi.
 */
class BrevoApiTransport extends AbstractTransport
{
    private string $apiKey;
    private string $endpoint = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(string $apiKey)
    {
        parent::__construct();
        $this->apiKey = $apiKey;
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $from = $email->getFrom()[0] ?? null;
        if (!$from) {
            throw new \RuntimeException('Brevo API: e-mail sem remetente (From).');
        }

        $payload = [
            'sender'      => $this->addressToArray($from),
            'to'          => array_map(fn (Address $a) => $this->addressToArray($a), $email->getTo()),
            'subject'     => $email->getSubject() ?? '',
            'htmlContent' => $email->getHtmlBody(),
        ];

        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }

        if ($cc = $email->getCc()) {
            $payload['cc'] = array_map(fn (Address $a) => $this->addressToArray($a), $cc);
        }

        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = array_map(fn (Address $a) => $this->addressToArray($a), $bcc);
        }

        if ($replyTo = $email->getReplyTo()) {
            $payload['replyTo'] = $this->addressToArray($replyTo[0]);
        }

        $response = Http::withHeaders([
                'api-key'      => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])
            ->timeout(15)
            ->post($this->endpoint, $payload);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Brevo API retornou HTTP ' . $response->status() . ': ' . $response->body()
            );
        }
    }

    private function addressToArray(Address $a): array
    {
        $out = ['email' => $a->getAddress()];
        if ($a->getName() !== '') {
            $out['name'] = $a->getName();
        }
        return $out;
    }

    public function __toString(): string
    {
        return 'brevo+api://';
    }
}
