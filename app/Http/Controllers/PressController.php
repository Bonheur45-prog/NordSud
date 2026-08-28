<?php

namespace App\Http\Controllers;

use App\Models\PressPost;
use Illuminate\View\View;

class PressController extends Controller
{
    public function index(string $locale): View
    {
        $posts = PressPost::press()
            ->published()
            ->with('translations')
            ->orderByDesc('published_at')
            ->get();

        return view('press.index', [
            'posts' => $posts,
            'locale' => $locale,
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        $post = PressPost::press()
            ->published()
            ->with('translations')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('press.show', [
            'post' => $post,
            'translation' => $post->translation($locale),
            'locale' => $locale,
        ]);
    }
}
