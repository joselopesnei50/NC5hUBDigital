<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pagina;
use App\Models\Post;
use App\Models\Servico;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $posts = Post::where('status', 'publicado')->latest()->take(3)->get();
        $servicos = Servico::where('status', 'ativo')->take(3)->get();
        $paginaHome = Pagina::where('slug', 'home')->where('status', 'publicado')->first();
        $clientesVitrine = Cliente::where('exibir_home', true)
            ->whereNotNull('logo_public_path')
            ->orderBy('razao_social')
            ->get();
        return view('public.home', compact('posts', 'servicos', 'paginaHome', 'clientesVitrine'));
    }

    public function blog()
    {
        $posts = Post::where('status', 'publicado')->latest()->paginate(9);
        return view('public.blog', compact('posts'));
    }

    public function post($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'publicado')->firstOrFail();
        return view('public.post', compact('post'));
    }

    public function servicos()
    {
        $servicos = Servico::where('status', 'ativo')->get();
        return view('public.servicos', compact('servicos'));
    }

    public function sitemap()
    {
        $paginasEstaticas = [
            ['loc' => route('home'),          'changefreq' => 'weekly',  'priority' => '1.0'],
            ['loc' => route('servicos'),      'changefreq' => 'monthly', 'priority' => '0.9'],
            ['loc' => route('blog'),          'changefreq' => 'weekly',  'priority' => '0.8'],
            ['loc' => route('analise.index'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('contato.index'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('privacidade'),   'changefreq' => 'yearly',  'priority' => '0.3'],
            ['loc' => route('termos'),        'changefreq' => 'yearly',  'priority' => '0.3'],
            ['loc' => route('cookies'),       'changefreq' => 'yearly',  'priority' => '0.3'],
        ];

        $posts = Post::where('status', 'publicado')->latest()->get();

        return response()
            ->view('public.sitemap', compact('paginasEstaticas', 'posts'))
            ->header('Content-Type', 'application/xml');
    }
}
