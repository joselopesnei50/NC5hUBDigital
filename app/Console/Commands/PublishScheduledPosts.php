<?php

namespace App\Console\Commands;

use App\Jobs\PublishScheduledPostJob;
use App\Models\ScheduledPost;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature   = 'posts:publish';
    protected $description = 'Dispara jobs para publicar todos os posts agendados vencidos no Facebook e Instagram.';

    public function handle(): int
    {
        $posts = ScheduledPost::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('Nenhum post agendado para publicar agora.');
            return self::SUCCESS;
        }

        foreach ($posts as $post) {
            PublishScheduledPostJob::dispatch($post->id);
        }

        $this->info("Disparados {$posts->count()} job(s) de publicação social.");
        return self::SUCCESS;
    }
}
