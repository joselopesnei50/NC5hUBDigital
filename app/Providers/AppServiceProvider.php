<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuracao;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
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

            // ---- Reply-To global (respostas do cliente vão para caixa monitorada) ----
            if (!empty($configs['mail_reply_to'])) {
                Mail::alwaysReplyTo(
                    $configs['mail_reply_to'],
                    $configs['mail_reply_to_name'] ?? ($configs['mail_from_name'] ?? null)
                );
            }

            // ---- Transporte SMTP Brevo ----
            if (!empty($configs['brevo_api_key'])) {
                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp.host', 'smtp-relay.brevo.com');
                Config::set('mail.mailers.smtp.port', 587);
                Config::set('mail.mailers.smtp.encryption', 'tls');
                Config::set('mail.mailers.smtp.username', $configs['brevo_smtp_login'] ?? '');
                Config::set('mail.mailers.smtp.password', $configs['brevo_api_key']);
                Config::set('mail.mailers.smtp.timeout', 15);
            }
        } catch (\Exception $e) {
            // não travar boot durante migrations / setup inicial
        }
    }
}
