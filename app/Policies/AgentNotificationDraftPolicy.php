<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AgentNotificationDraft;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgentNotificationDraftPolicy
{
    use HandlesAuthorization;

    /**
     * Valida se o usuário tem permissão para visualizar este rascunho (Isolamento de Tenant).
     */
    public function view(User $user, AgentNotificationDraft $draft): bool
    {
        // Garante que o usuário só vê os rascunhos que pertencem ao seu próprio negócio (cliente_id)
        return $user->cliente_id === $draft->cliente_id;
    }

    /**
     * Valida se o usuário tem permissão para aprovar e disparar a notificação.
     */
    public function approve(User $user, AgentNotificationDraft $draft): bool
    {
        // Se quisermos criar níveis hierárquicos depois (só o 'Dono' pode aprovar, e o 'Operador' não),
        // é nesta função que a regra vai entrar. Por hora, a regra básica de Tenant se aplica.
        return $user->cliente_id === $draft->cliente_id;
    }

    /**
     * Valida se o usuário tem permissão para descartar a notificação.
     */
    public function discard(User $user, AgentNotificationDraft $draft): bool
    {
        return $user->cliente_id === $draft->cliente_id;
    }
}
