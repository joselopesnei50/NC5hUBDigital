<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContaClienteCriadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $senha;
    public string $painelUrl;

    public function __construct(User $user, string $senha)
    {
        $this->user = $user;
        $this->senha = $senha;
        $this->painelUrl = route('login');
    }

    public function build()
    {
        return $this->subject('Sua conta na NC5 Hub foi criada')
                    ->view('emails.conta_cliente_criada');
    }
}
