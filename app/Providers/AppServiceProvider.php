<?php

namespace App\Providers;

use App\Mail\Transport\BrevoApiTransport;
use App\Models\Configuracao;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Força HTTPS em URLs geradas por route() e url() quando estivermos
        // atrás de proxy/SSL (produção). Evita link http:// no e-mail com SSL ativo.
        if ($this->app->environment('production') || str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }

        // Registra o transport 'brevo-api' no MailManager.
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

            // ---- Auto-provisiona tabelas/colunas Meta Cloud API & Redes Sociais em produção ----
            $this->ensureMetaAndSocialSchema();
        } catch (\Exception $e) {
            // não trava boot durante migrations
        }
    }

    /**
     * Garante que as colunas da Meta Cloud API e as tabelas de Redes Sociais
     * existam automaticamente no banco de produção sem exigir comando SSH manual.
     */
    private function ensureMetaAndSocialSchema(): void
    {
        if (Cache::get('nc5_meta_social_schema_v1') === true) {
            return;
        }

        try {
            if (Schema::hasTable('whatsapp_instances') && !Schema::hasColumn('whatsapp_instances', 'provider')) {
                Schema::table('whatsapp_instances', function (Blueprint $table) {
                    $table->string('provider', 20)->default('evolution')->after('instance_token');
                    $table->string('waba_id', 64)->nullable()->after('provider');
                    $table->string('phone_number_id', 64)->nullable()->index()->after('waba_id');
                    $table->text('graph_access_token')->nullable()->after('phone_number_id');
                });
            }

            if (!Schema::hasTable('social_accounts')) {
                Schema::create('social_accounts', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cliente_id')->index();
                    $table->string('platform', 30)->default('facebook');
                    $table->string('page_id', 100)->index();
                    $table->string('page_name', 191);
                    $table->text('page_picture')->nullable();
                    $table->text('access_token');
                    $table->timestamp('token_expires_at')->nullable();
                    $table->string('instagram_business_id', 100)->nullable()->index();
                    $table->string('instagram_username', 100)->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();

                    $table->unique(['cliente_id', 'page_id']);
                });
            }

            if (!Schema::hasTable('scheduled_posts')) {
                Schema::create('scheduled_posts', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cliente_id')->index();
                    $table->unsignedBigInteger('social_account_id')->index();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->string('platform', 20)->default('both');
                    $table->string('format', 20)->default('feed');
                    $table->text('caption')->nullable();
                    $table->text('media_url')->nullable();
                    $table->json('media_items')->nullable();
                    $table->string('media_type', 20)->default('image');
                    $table->dateTime('scheduled_at')->index();
                    $table->string('status', 20)->default('pending')->index();
                    $table->string('facebook_post_id', 120)->nullable();
                    $table->string('instagram_post_id', 120)->nullable();
                    $table->text('error_message')->nullable();
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('facebook_data_deletion_requests')) {
                Schema::create('facebook_data_deletion_requests', function (Blueprint $table) {
                    $table->id();
                    $table->string('facebook_user_id', 100)->index();
                    $table->string('confirmation_code', 64)->unique();
                    $table->string('status', 30)->default('completed');
                    $table->timestamp('completed_at')->nullable();
                    $table->timestamps();
                });
            }

            Cache::forever('nc5_meta_social_schema_v1', true);
        } catch (\Throwable $e) {
            // Ignora silenciosamente caso o banco esteja em manutenção
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
