<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SystemCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function enter(Request $request)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);

        SystemCliente::get();
        $target = SystemCliente::user();

        $request->session()->put('impersonating_admin_user_id', $admin->id);
        Auth::login($target);

        $route = match ($request->query('target')) {
            'whatsapp.inbox' => 'customer.whatsapp.inbox',
            'whatsapp.bot' => 'customer.whatsapp.bot.index',
            'whatsapp.dashboard' => 'customer.whatsapp.dashboard',
            'google-business' => 'customer.google-business.index',
            default => 'customer.whatsapp.conexao',
        };

        return redirect()->route($route);
    }

    public function leave(Request $request)
    {
        $adminId = $request->session()->pull('impersonating_admin_user_id');
        if (!$adminId) {
            return redirect()->route('customer.index');
        }

        $admin = User::find($adminId);
        if (!$admin || $admin->role !== 'admin') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Sessão de admin perdida. Faça login novamente.');
        }

        Auth::login($admin);
        return redirect()->route('admin.dashboard')->with('success', 'Voltou ao painel admin.');
    }
}
