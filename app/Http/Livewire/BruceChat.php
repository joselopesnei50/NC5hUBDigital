<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AgentConversation;
use App\Agent\Chat\BruceConversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BruceChat extends Component
{
    public $conversationId;
    public string $message = '';

    public function mount()
    {
        // Se já existir uma conversa ativa hoje, ele a reaproveita para manter o contexto.
        $user = Auth::user();
        
        if ($user && $user->cliente_id) {
            $conversation = AgentConversation::firstOrCreate(
                [
                    'cliente_id' => $user->cliente_id,
                    'user_id' => $user->id,
                    'status' => 'active'
                ],
                ['title' => 'Sessão de Atendimento']
            );

            $this->conversationId = $conversation->id;
        }
    }

    public function sendMessage(BruceConversation $bruce)
    {
        $this->validate(['message' => 'required|string|max:1000']);

        if (!$this->conversationId) {
            $this->addError('limit', 'Sessão inválida. Atualize a página.');
            return;
        }

        $conversation = AgentConversation::find($this->conversationId);
        
        // ==========================================
        // GUARDRAIL FINANCEIRO (DISJUNTOR)
        // Impede que um cliente faça o sistema torrar mais de 50.000 tokens em uma única sala
        // ==========================================
        if ($conversation->accumulated_tokens > 50000) {
            $this->addError('limit', 'Você atingiu o teto de consultas simultâneas deste bate-papo. Por favor, encerre e abra um novo chat.');
            return;
        }

        // Salvamos temporariamente o input pois limparemos a tela instantaneamente
        $userText = $this->message;
        $this->message = ''; 

        try {
            // Este método vai segurar a conexão do PHP por uns segundos, mas o front não vai 
            // travar, pois o Livewire exibe a flag "wire:loading" para o usuário.
            $bruce->handleTurn($conversation, $userText);
        } catch (\Exception $e) {
            Log::error("[Livewire BruceChat] Erro fatal no chat: " . $e->getMessage());
            $this->addError('limit', 'Ops, o Bruce não conseguiu responder no momento. Tente novamente mais tarde.');
        }
    }

    public function render()
    {
        // Na tela, o cliente SÓ VÊ as interações dele e do robô.
        // O cliente não vê o Bruce chamando as `tools` nos bastidores (ocultando o sistema e SQLs).
        $conversation = AgentConversation::with(['messages' => function($q) {
            $q->whereIn('role', ['user', 'assistant'])->orderBy('created_at', 'asc');
        }])->find($this->conversationId);

        return view('livewire.bruce-chat', [
            'messages' => $conversation ? $conversation->messages : []
        ]);
    }
}
