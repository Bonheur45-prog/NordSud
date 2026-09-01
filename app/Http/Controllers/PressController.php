<?php

namespace App\Http\Controllers;

use App\Models\PressComment;
use App\Models\PressPost;
use App\Models\PressPageSettings;
use App\Models\PressTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PressController extends Controller
{
    public function index(string $locale, Request $request): View
    {
        $query = PressPost::press()
            ->published()
            ->with(['translations', 'author', 'tags'])
            ->withCount(['comments' => fn ($q) => $q->where('approved', true)]);

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $activeTag = null;
        $tagSlug = $request->query('tag');
        if (filled($tagSlug)) {
            $activeTag = PressTag::where('slug', $tagSlug)->first();
            if ($activeTag) {
                $query->whereHas('tags', fn ($q) => $q->where('press_tags.id', $activeTag->id));
            }
        }

        $posts = $query->orderByDesc('published_at')->get();

        return view('press.index', [
            'posts' => $posts,
            'locale' => $locale,
            'search' => $search,
            'activeTag' => $activeTag,
            'popularPosts' => $this->popularPosts($locale),
            'popularTags' => $this->popularTags(),
            'pageSettings' => PressPageSettings::first(),
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        $post = PressPost::press()
            ->published()
            ->with(['translations', 'author', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('press.show', [
            'post' => $post,
            'translation' => $post->translation($locale),
            'locale' => $locale,
            'comments' => $post->approvedComments()->get(),
            'previousPost' => $post->previousPost(),
            'nextPost' => $post->nextPost(),
            'popularPosts' => $this->popularPosts($locale, exceptId: $post->id),
            'popularTags' => $this->popularTags(),
        ]);
    }

    public function storeComment(Request $request, string $locale, string $slug): RedirectResponse
    {
        $post = PressPost::press()->published()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'author_email' => ['nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            // Honeypot: the "Website (optional)" field. Real visitors leave
            // it blank; most spam bots fill every field they find.
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        PressComment::create([
            'press_post_id' => $post->id,
            'author_name' => $data['author_name'],
            'author_email' => $data['author_email'] ?? null,
            'body' => $data['body'],
            'approved' => false,
        ]);

        return redirect()
            ->route('press.show', ['locale' => $locale, 'slug' => $slug])
            ->with('comment_success', true)
            ->withFragment('comments');
    }

    /**
     * Hand-curated (is_featured) posts first, topped up with the most
     * recent ones so the sidebar never looks empty on a fresh install.
     */
    protected function popularPosts(string $locale, ?int $exceptId = null): Collection
    {
        $base = PressPost::press()->published()->with('translations')
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId));

        $featured = (clone $base)->featured()->orderByDesc('published_at')->limit(3)->get();

        if ($featured->count() < 3) {
            $fill = (clone $base)
                ->whereNotIn('id', $featured->pluck('id'))
                ->orderByDesc('published_at')
                ->limit(3 - $featured->count())
                ->get();

            $featured = $featured->concat($fill);
        }

        return $featured->take(3);
    }

    protected function popularTags(): Collection
    {
        return PressTag::withCount('posts')
            ->orderByDesc('posts_count')
            ->get()
            ->filter(fn (PressTag $tag) => $tag->posts_count > 0)
            ->values();
    }
}
