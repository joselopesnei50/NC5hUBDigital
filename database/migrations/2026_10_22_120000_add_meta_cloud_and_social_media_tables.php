<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Evolução de whatsapp_instances para suportar Meta Cloud API Oficial ao lado da Evolution API
        if (Schema::hasTable('whatsapp_instances')) {
            Schema::table('whatsapp_instances', function (Blueprint $table) {
                if (!Schema::hasColumn('whatsapp_instances', 'provider')) {
                    $table->string('provider', 20)->default('evolution')->after('cliente_id')->index();
                }
                if (!Schema::hasColumn('whatsapp_instances', 'waba_id')) {
                    $table->string('waba_id')->nullable()->after('provider')->index();
                }
                if (!Schema::hasColumn('whatsapp_instances', 'phone_number_id')) {
                    $table->string('phone_number_id')->nullable()->after('waba_id')->index();
                }
                if (!Schema::hasColumn('whatsapp_instances', 'graph_access_token')) {
                    $table->text('graph_access_token')->nullable()->after('phone_number_id');
                }
            });

            DB::table('whatsapp_instances')
                ->whereNull('provider')
                ->orWhere('provider', '')
                ->update(['provider' => 'evolution']);
        }

        // 2. Contas sociais conectadas por cliente (Facebook Pages + Instagram Business)
        if (!Schema::hasTable('social_accounts')) {
            Schema::create('social_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
                $table->string('platform')->default('facebook'); // facebook | instagram
                $table->string('page_id');
                $table->string('page_name');
                $table->text('page_picture')->nullable();
                $table->text('access_token');
                $table->timestamp('token_expires_at')->nullable();
                $table->string('instagram_business_id')->nullable();
                $table->string('instagram_username')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['cliente_id', 'is_active']);
                $table->unique(['cliente_id', 'page_id']);
            });
        }

        // 3. Posts agendados e publicados (Feed, Reels, Carrossel, Stories)
        if (!Schema::hasTable('scheduled_posts')) {
            Schema::create('scheduled_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
                $table->foreignId('social_account_id')->nullable()->constrained('social_accounts')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('platform'); // facebook | instagram | both
                $table->string('format', 20)->default('feed'); // feed | story
                $table->text('caption');
                $table->text('media_url')->nullable();
                $table->json('media_items')->nullable();
                $table->string('media_type')->default('none'); // none | image | video
                $table->timestamp('scheduled_at');
                $table->string('status')->default('scheduled'); // draft | scheduled | published | failed | cancelled
                $table->string('facebook_post_id')->nullable();
                $table->string('instagram_post_id')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();

                $table->index(['cliente_id', 'status']);
                $table->index(['scheduled_at', 'status']);
            });
        }

        // 4. Conformidade Meta LGPD (Data Deletion Callback)
        if (!Schema::hasTable('facebook_data_deletion_requests')) {
            Schema::create('facebook_data_deletion_requests', function (Blueprint $table) {
                $table->id();
                $table->string('facebook_user_id')->nullable()->index();
                $table->string('confirmation_code', 64)->unique();
                $table->string('status', 30)->default('received'); // received | processing | completed | rejected
                $table->text('notes')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_data_deletion_requests');
        Schema::dropIfExists('scheduled_posts');
        Schema::dropIfExists('social_accounts');

        if (Schema::hasTable('whatsapp_instances')) {
            Schema::table('whatsapp_instances', function (Blueprint $table) {
                $cols = array_filter(
                    ['provider', 'waba_id', 'phone_number_id', 'graph_access_token'],
                    fn ($c) => Schema::hasColumn('whatsapp_instances', $c)
                );
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
