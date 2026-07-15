<?php

namespace App\Providers;

use App\Mail\Transport\BrevoApiTransport;
use App\Models\Configuracao;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Registra o transport 'brevo-api' no MailManager.
        // O Laravel resolve isso quando a config aponta para transport: 'brevo-api'.
        $this->app->resolving('mail.manager', function (MailManager $manager) {
            $manager->extend('brevo-api', function (array $config) {
                return new BrevoApiTransport($config['key'] ?? '');
            });
        });

        if (!Schema::hasTable('configuracoes')) {
            return;
        }

        try {
            $configs = Configuracao::todas();

            // ---- Identidade global do remetente ----
            if (!empty($configs['mail_from_address'])) {
                Config::set('mail.from.address', $configs['mail_from_address']);
                Config::set('mail.from.name', $configs['mail_from_name'] ?? 'NC5 Hub');
            }

            // ---- Reply-To global ----
            if (!empty($configs['mail_reply_to'])) {
                Mail::alwaysReplyTo(
                    $configs['mail_reply_to'],
                    $configs['mail_reply_to_name'] ?? ($configs['mail_from_name'] ?? null)
                );
            }

            // ---- Detecta e configura o motor Brevo ----
            $this->configurarBrevo($configs);
        } catch (\Exception $e) {
            // não trava boot durante migrations
        }
    }

    /**
     * Decide entre API HTTP (xkeysib-...) e SMTP relay (xsmtpsib-...)
     * conforme o prefixo da chave. Prioriza API se ambos estiverem preenchidos.
     */
    private function configurarBrevo(array $configs): void
    {
        $chave = trim($configs['brevo_api_key'] ?? '');
        if ($chave === '') {
            return;
        }

        $ehApiKey  = str_starts_with($chave, 'xkeysib-');
        $ehSmtpKey = str_starts_with($chave, 'xsmtpsib-');

        // Modo API HTTP — sem login, é o modo do Vivensi
        if ($ehApiKey || (!$ehSmtpKey && empty($configs['brevo_smtp_login']))) {
            Config::set('mail.mailers.brevo', [
                'transport' => 'brevo-api',
                'key'       => $chave,
            ]);
            Config::set('mail.default', 'brevo');
            return;
        }

        // Modo SMTP relay clássico
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp-relay.brevo.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.username', $configs['brevo_smtp_login'] ?? '');
        Config::set('mail.mailers.smtp.password', $chave);
        Config::set('mail.mailers.smtp.timeout', 15);
    }
}
