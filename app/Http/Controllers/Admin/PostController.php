<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'status' => 'required|in:rascunho,publicado'
        ]);

        Post::create([
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo),
            'conteudo' => $request->conteudo,
            'status' => $request->status
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Post criado com sucesso!');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'status' => 'required|in:rascunho,publicado'
        ]);

        $post->update([
            'titulo' => $request->titulo,
            'slug' => Str::slug($request->titulo),
            'conteudo' => $request->conteudo,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Post atualizado.');
    }

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post removido.');
    }
}
