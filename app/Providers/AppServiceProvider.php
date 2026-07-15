<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuracao;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Se a tabela de configurações já existir, injeta as configurações de e-mail (Brevo SMTP)
        if (Schema::hasTable('configuracoes')) {
            try {
                $configs = Configuracao::todas();

                // Configura o remetente global
                if (!empty($configs['mail_from_address'])) {
                    Config::set('mail.from.address', $configs['mail_from_address']);
                    Config::set('mail.from.name', $configs['mail_from_name'] ?? 'NC5 Hub');
                }

                // Configura o Brevo como SMTP se a chave existir
                if (!empty($configs['brevo_api_key'])) {
                    Config::set('mail.default', 'smtp');
                    Config::set('mail.mailers.smtp.host', 'smtp-relay.brevo.com');
                    Config::set('mail.mailers.smtp.port', 587);
                    Config::set('mail.mailers.smtp.encryption', 'tls');
                    Config::set('mail.mailers.smtp.username', $configs['brevo_smtp_login'] ?? '');
                    Config::set('mail.mailers.smtp.password', $configs['brevo_api_key']);
                }
            } catch (\Exception $e) {
                // Ignore errors during boot (e.g. when migrating)
            }
        }
    }
}
