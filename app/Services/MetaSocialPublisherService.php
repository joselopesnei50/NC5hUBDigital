<?php

namespace App\Services;

use App\Models\ScheduledPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaSocialPublisherService
{
    private string $graphVersion;

    public function __construct()
    {
        $this->graphVersion = (string) config('whatsapp.meta_api_version', 'v22.0');
    }

    /** Publica o post agendado nas plataformas configuradas */
    public function publish(ScheduledPost $post): bool
    {
        $account = $post->account;
        if (!$account || !$account->is_active) {
            $this->fail($post, 'Conta social inativa ou não encontrada.');
            return false;
        }

        $token   = $account->access_token;
        $success = false;
        $format  = $post->format ?? 'feed';
        $isStory = $format === 'story';

        if (in_array($post->platform, ['facebook', 'both'], true) && !$isStory) {
            $fb = $this->publishToFacebook($post, $account->page_id, $token);
            if (!empty($fb['id'])) {
                $post->facebook_post_id = $fb['id'];
                $success = true;
            } else {
                $this->fail($post, $fb['error'] ?? 'Falha ao publicar no Facebook.');
                return false;
            }
        }

        if (in_array($post->platform, ['instagram', 'both'], true) && $account->instagram_business_id) {
            $ig = $this->publishToInstagram($post, $account->instagram_business_id, $token);
            if (!empty($ig['id'])) {
                $post->instagram_post_id = $ig['id'];
                $success = true;
            } else {
                $this->fail($post, $ig['error'] ?? 'Falha ao publicar no Instagram.');
                return false;
            }
        }

        if ($success) {
            $post->status        = 'published';
            $post->error_message = null;
            $post->save();
        }

        return $success;
    }

    private function publishInstagramStory(ScheduledPost $post, string $igAccountId, string $token, array $item): array
    {
        $mediaUrl = $this->absoluteMediaUrl($item['url']);
        $type     = $item['type'] ?? 'image';

        $payload = [
            'media_type'   => 'STORIES',
            'access_token' => $token,
        ];
        if ($type === 'image') {
            $payload['image_url'] = $mediaUrl;
        } elseif ($type === 'video') {
            $payload['video_url'] = $mediaUrl;
        } else {
            return ['id' => null, 'error' => 'Story precisa ser imagem ou vídeo.'];
        }

        $containerRes = Http::post(
            "https://graph.facebook.com/{$this->graphVersion}/{$igAccountId}/media",
            $payload
        );

        if (!$containerRes->successful()) {
            $err = (array) ($containerRes->json('error') ?? []);
            return ['id' => null, 'error' => $this->friendlyError($err)];
        }

        $containerId = $containerRes->json('id');
        $waitErr = $this->waitForInstagramContainer($containerId, $token);
        if ($waitErr !== null) {
            return ['id' => null, 'error' => $waitErr];
        }

        $publishRes = Http::post(
            "https://graph.facebook.com/{$this->graphVersion}/{$igAccountId}/media_publish",
            ['creation_id' => $containerId, 'access_token' => $token]
        );

        if (!$publishRes->successful()) {
            $err = (array) ($publishRes->json('error') ?? []);
            return ['id' => null, 'error' => $this->friendlyError($err)];
        }

        return ['id' => $publishRes->json('id'), 'error' => null];
    }

    private function publishInstagramCarousel(ScheduledPost $post, string $igAccountId, string $token, array $items): array
    {
        if (count($items) > 10) {
            $items = array_slice($items, 0, 10);
        }

        $childIds = [];

        foreach ($items as $idx => $item) {
            $mediaUrl = $this->absoluteMediaUrl($item['url']);
            $type     = $item['type'] ?? 'image';

            $payload = [
                'is_carousel_item' => true,
                'access_token'     => $token,
            ];
            if ($type === 'image') {
                $payload['image_url']  = $mediaUrl;
                $payload['media_type'] = 'IMAGE';
            } elseif ($type === 'video') {
                $payload['video_url']  = $mediaUrl;
                $payload['media_type'] = 'VIDEO';
            } else {
                continue;
            }

            $res = Http::post(
                "https://graph.facebook.com/{$this->graphVersion}/{$igAccountId}/media",
                $payload
            );

            if (!$res->successful()) {
                $err = (array) ($res->json('error') ?? []);
                return [
                    'id'    => null,
                    'error' => "Falha na mídia #" . ($idx + 1) . " do carrossel: " . $this->friendlyError($err),
                ];
            }

            $childIds[] = $res->json('id');
        }

        if (empty($childIds)) {
            return ['id' => null, 'error' => 'Nenhuma mídia do carrossel pôde ser processada.'];
        }

        foreach ($childIds as $idx => $cid) {
            $waitErr = $this->waitForInstagramContainer($cid, $token);
            if ($waitErr !== null) {
                return [
                    'id'    => null,
                    'error' => "Mídia #" . ($idx + 1) . " do carrossel travou processando na Meta. " . $waitErr,
                ];
            }
        }

        $carouselRes = Http::post(
            "https://graph.facebook.com/{$this->graphVersion}/{$igAccountId}/media",
            [
                'media_type'   => 'CAROUSEL',
                'caption'      => $post->caption,
                'children'     => implode(',', $childIds),
                'access_token' => $token,
            ]
        );

        if (!$carouselRes->successful()) {
            $err = (array) ($carouselRes->json('error') ?? []);
            return ['id' => null, 'error' => 'Falha ao montar carrossel: ' . $this->friendlyError($err)];
        }

        $carouselId = $carouselRes->json('id');
        $waitErr = $this->waitForInstagramContainer($carouselId, $token);
        if ($waitErr !== null) {
            return ['id' => null, 'error' => $waitErr];
        }

        $publishRes = Http::post(
            "https://graph.facebook.com/{$this->graphVersion}/{$igAccountId}/media_publish",
            ['creation_id' => $carouselId, 'access_token' => $token]
        );

        if (!$publishRes->successful()) {
            $err = (array) ($publishRes->json('error') ?? []);
            return ['id' => null, 'error' => 'Falha ao publicar carrossel: ' . $this->friendlyError($err)];
        }

        return ['id' => $publishRes->json('id'), 'error' => null];
    }

    private function waitForInstagramContainer(string $containerId, string $token, int $maxTries = 8): ?string
    {
        $delays = [1, 1, 2, 2, 3, 3, 4, 5];

        for ($i = 0; $i < $maxTries; $i++) {
            sleep($delays[$i] ?? 3);

            $res = Http::get(
                "https://graph.facebook.com/{$this->graphVersion}/{$containerId}",
                ['fields' => 'status_code', 'access_token' => $token]
            );

            if (!$res->successful()) {
                return null;
            }

            $status = $res->json('status_code');

            if ($status === 'FINISHED' || $status === 'PUBLISHED') {
                return null;
            }
            if ($status === 'ERROR') {
                return 'O Instagram rejeitou a mídia. Verifique a proporção e o formato (JPG/PNG recomendado).';
            }
            if ($status === 'EXPIRED') {
                return 'O container do Instagram expirou (24h). Crie o post novamente.';
            }
        }

        return 'O Instagram demorou para processar a mídia. Tente novamente em alguns segundos.';
    }

    private function absoluteMediaUrl(?string $url): ?string
    {
        if (!$url) return null;
        if (preg_match('#^https?://#i', $url)) return $url;
        return url($url);
    }

    private function friendlyError(array $err): string
    {
        $code    = isset($err['code']) ? (int) $err['code'] : null;
        $subcode = isset($err['error_subcode']) ? (int) $err['error_subcode'] : null;
        $msg     = trim((string) ($err['message'] ?? ''));

        $friendly = match (true) {
            $code === 190 => 'Sessão do Facebook expirou. Reconecte sua conta em Redes Sociais.',
            $code === 200 || $code === 10 => 'O aplicativo não tem permissão para publicar nesta Página. Reconecte a conta autorizando todas as permissões.',
            $code === 100 && $subcode === 33 => 'O objeto solicitado não existe ou não pertence à Página conectada.',
            $code === 100 => 'Parâmetro inválido na chamada Meta. Verifique se a mídia tem URL pública acessível e formato correto.',
            $code === 4   => 'Limite de chamadas da Meta atingido temporariamente. Aguarde alguns minutos.',
            $code === 368 => 'Sua Página do Facebook está temporariamente bloqueada para publicação.',
            $code === 1487 => 'A URL da mídia não pôde ser baixada pela Meta. Confirme que o site está em domínio público (não localhost).',
            $code === 9007 => 'O Instagram ainda estava processando a mídia. Tente novamente em alguns segundos.',
            default => null,
        };

        return $friendly
            ?? ($msg !== '' ? $msg . ($code ? " (code {$code})" : '') : 'Erro desconhecido da Meta.');
    }

    private function publishToFacebook(ScheduledPost $post, string $pageId, string $token): array
    {
        $mediaUrl = $this->absoluteMediaUrl($post->media_url);

        if ($mediaUrl && $post->media_type === 'image') {
            return $this->publishFacebookPhotoPost($post, $pageId, $token, $mediaUrl);
        }

        if ($mediaUrl && $post->media_type === 'video') {
            $res = Http::post("https://graph.facebook.com/{$this->graphVersion}/{$pageId}/videos", [
                'message'      => $post->caption,
                'file_url'     => $mediaUrl,
                'access_token' => $token,
            ]);
            return $this->parseFacebookPublishResponse($res, $post, $pageId);
        }

        $res = Http::post("https://graph.facebook.com/{$this->graphVersion}/{$pageId}/feed", [
            'message'      => $post->caption,
            'access_token' => $token,
        ]);
        return $this->parseFacebookPublishResponse($res, $post, $pageId);
    }

    private function publishFacebookPhotoPost(ScheduledPost $post, string $pageId, string $token, string $mediaUrl): array
    {
        $uploadRes = Http::post(
            "https://graph.facebook.com/{$this->graphVersion}/{$pageId}/photos",
            [
                'url'          => $mediaUrl,
                'published'    => false,
                'access_token' => $token,
            ]
        );

        if (!$uploadRes->successful()) {
            $err = (array) ($uploadRes->json('error') ?? []);
            return ['id' => null, 'error' => $this->friendlyError($err)];
        }

        $mediaFbid = $uploadRes->json('id');
        if (!$mediaFbid) {
            return ['id' => null, 'error' => 'Meta não retornou ID da foto no upload.'];
        }

        $feedRes = Http::post(
            "https://graph.facebook.com/{$this->graphVersion}/{$pageId}/feed",
            [
                'message'        => $post->caption,
                'attached_media' => json_encode([['media_fbid' => $mediaFbid]]),
                'access_token'   => $token,
            ]
        );

        return $this->parseFacebookPublishResponse($feedRes, $post, $pageId);
    }

    private function parseFacebookPublishResponse($response, ScheduledPost $post, string $pageId): array
    {
        if (!$response->successful()) {
            $body = $response->json() ?? [];
            $err  = (array) ($body['error'] ?? []);
            Log::error('Facebook publish failed', [
                'post_id' => $post->id,
                'status'  => $response->status(),
                'error'   => $err,
            ]);
            return ['id' => null, 'error' => $this->friendlyError($err)];
        }

        $rawId  = $response->json('id');
        $postId = $response->json('post_id');

        if (!$postId && $rawId && !str_contains($rawId, '_')) {
            $postId = "{$pageId}_{$rawId}";
        }

        return ['id' => $postId ?? $rawId, 'error' => null];
    }

    private function publishToInstagram(ScheduledPost $post, string $igAccountId, string $token): array
    {
        $items = $post->mediaList();

        if (empty($items)) {
            return ['id' => null, 'error' => 'O Instagram exige pelo menos uma imagem ou vídeo anexado ao post.'];
        }

        if (($post->format ?? 'feed') === 'story') {
            return $this->publishInstagramStory($post, $igAccountId, $token, $items[0]);
        }

        if (count($items) >= 2) {
            return $this->publishInstagramCarousel($post, $igAccountId, $token, $items);
        }

        $only     = $items[0];
        $mediaUrl = $this->absoluteMediaUrl($only['url']);
        $type     = $only['type'] ?? 'image';

        $containerPayload = [
            'caption'      => $post->caption,
            'access_token' => $token,
        ];

        if ($type === 'image') {
            $containerPayload['image_url']  = $mediaUrl;
            $containerPayload['media_type'] = 'IMAGE';
        } elseif ($type === 'video') {
            $containerPayload['video_url']  = $mediaUrl;
            $containerPayload['media_type'] = 'REELS';
        }

        $containerRes = Http::post(
            "https://graph.facebook.com/{$this->graphVersion}/{$igAccountId}/media",
            $containerPayload
        );

        if (!$containerRes->successful()) {
            $err = (array) ($containerRes->json('error') ?? []);
            return ['id' => null, 'error' => $this->friendlyError($err)];
        }

        $containerId = $containerRes->json('id');
        $waitError = $this->waitForInstagramContainer($containerId, $token);
        if ($waitError !== null) {
            return ['id' => null, 'error' => $waitError];
        }

        $publishRes = Http::post(
            "https://graph.facebook.com/{$this->graphVersion}/{$igAccountId}/media_publish",
            ['creation_id' => $containerId, 'access_token' => $token]
        );

        if (!$publishRes->successful()) {
            $err = (array) ($publishRes->json('error') ?? []);
            return ['id' => null, 'error' => $this->friendlyError($err)];
        }

        return ['id' => $publishRes->json('id'), 'error' => null];
    }

    private function fail(ScheduledPost $post, string $message): void
    {
        $post->status        = 'failed';
        $post->error_message = $message;
        $post->save();
        Log::error("ScheduledPost #{$post->id} failed: {$message}");
    }
}
