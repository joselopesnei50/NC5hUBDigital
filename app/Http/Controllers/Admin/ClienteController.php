<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContaClienteCriadaMail;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('user')->paginate(10);
        return view('admin.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'tipo_pessoa' => 'required|in:PF,PJ',
            'cpf_cnpj' => 'required|string',
            'razao_social' => 'required|string',
            'telefone' => 'nullable|string',
        ]);

        // Cria o usuário de login com senha temporária
        $senhaTemp = Str::random(10);
        $user = User::create([
            'name'     => $request->nome,
            'email'    => $request->email,
            'password' => Hash::make($senhaTemp),
            'role'     => 'cliente',
        ]);

        $cliente = Cliente::create([
            'user_id'      => $user->id,
            'tipo_pessoa'  => $request->tipo_pessoa,
            'cpf_cnpj'     => $request->cpf_cnpj,
            'razao_social' => $request->razao_social,
            'telefone'     => $request->telefone,
            'status'       => 'ativo',
        ]);

        // Vincula o owner ao próprio cliente pra habilitar múltiplos usuários no painel
        $user->update(['cliente_id' => $cliente->id]);

        // Envia e-mail de boas-vindas com a senha temporária
        $emailEnviado = false;
        try {
            Mail::to($user->email)->send(new ContaClienteCriadaMail($user, $senhaTemp));
            $emailEnviado = true;
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail de boas-vindas para ' . $user->email . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.clientes.index')
            ->with('cliente_criado', [
                'nome'         => $user->name,
                'email'        => $user->email,
                'senha'        => $senhaTemp,
                'email_enviado'=> $emailEnviado,
            ]);
    }

    public function show($id)
    {
        $cliente = Cliente::with(['user', 'users', 'contratos', 'faturas', 'materiais', 'tickets'])->findOrFail($id);
        return view('admin.clientes.show', compact('cliente'));
    }

    public function usersStore(Request $request, Cliente $cliente)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($cliente->users()->count() >= Cliente::MAX_USERS_PAINEL) {
            return back()->with('error', 'Limite de ' . Cliente::MAX_USERS_PAINEL . ' usuários por cliente atingido.');
        }

        $senhaTemp = Str::random(10);
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($senhaTemp),
            'role'       => 'cliente',
            'cliente_id' => $cliente->id,
        ]);

        $emailEnviado = false;
        try {
            Mail::to($user->email)->send(new ContaClienteCriadaMail($user, $senhaTemp));
            $emailEnviado = true;
        } catch (\Throwable $e) {
            Log::error('Falha ao enviar e-mail pro usuário extra ' . $user->email . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.clientes.show', $cliente->id)
            ->with('usuario_criado', [
                'nome'          => $user->name,
                'email'         => $user->email,
                'senha'         => $senhaTemp,
                'email_enviado' => $emailEnviado,
            ]);
    }

    public function usersDestroy(Cliente $cliente, User $user)
    {
        if ((int) $user->cliente_id !== (int) $cliente->id) {
            abort(404);
        }

        if ((int) $cliente->user_id === (int) $user->id) {
            return back()->with('error', 'O usuário titular do cliente não pode ser removido por aqui. Edite o cadastro do cliente.');
        }

        $user->delete();

        return redirect()->route('admin.clientes.show', $cliente->id)
            ->with('success', 'Usuário removido do painel do cliente.');
    }

    public function edit($id)
    {
        $cliente = Cliente::with('user')->findOrFail($id);
        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::with('user')->findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $cliente->user_id,
            'tipo_pessoa' => 'required|in:PF,PJ',
            'cpf_cnpj' => 'required|string',
            'razao_social' => 'required|string',
            'telefone' => 'nullable|string',
            'status' => 'required|in:ativo,inativo',
            'logo_public' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'exibir_home' => 'nullable|boolean',
            'remover_logo' => 'nullable|boolean',
        ]);

        $cliente->user->update([
            'name' => $request->nome,
            'email' => $request->email,
        ]);

        $dados = [
            'tipo_pessoa' => $request->tipo_pessoa,
            'cpf_cnpj' => $request->cpf_cnpj,
            'razao_social' => $request->razao_social,
            'telefone' => $request->telefone,
            'status' => $request->status,
            'exibir_home' => $request->boolean('exibir_home'),
        ];

        if ($request->boolean('remover_logo') && $cliente->logo_public_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($cliente->logo_public_path);
            $dados['logo_public_path'] = null;
        }

        if ($request->hasFile('logo_public')) {
            if ($cliente->logo_public_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cliente->logo_public_path);
            }
            $dados['logo_public_path'] = $request->file('logo_public')->store('logos-clientes', 'public');
        }

        $cliente->update($dados);

        return redirect()->route('admin.clientes.show', $cliente->id)
                         ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $cliente = Cliente::with('user')->findOrFail($id);
        $user = $cliente->user;
        $cliente->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.clientes.index')
                         ->with('success', 'Cliente removido do sistema.');
    }
}
