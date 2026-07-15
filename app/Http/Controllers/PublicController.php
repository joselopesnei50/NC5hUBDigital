<?php

namespace App\Http\Controllers;

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
        return view('public.home', compact('posts', 'servicos', 'paginaHome'));
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
}
