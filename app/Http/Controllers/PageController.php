<?php

namespace App\Http\Controllers;

use App\Models\ClientReference;
use App\Models\Page;
use App\Models\PressPost;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $page = Page::published()->where('slug', 'home')->firstOrFail();

        $pressPosts = PressPost::press()
            ->published()
            ->with('translations')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return $this->render($page, $pressPosts);
    }

    public function show(string $locale, string $slug): View
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        return $this->render($page);
    }

    protected function render(Page $page, $pressPosts = null): View
    {
        $clientReferences = in_array($page->template, ['home', 'references'], true)
            ? ClientReference::ordered()->get()
            : collect();

        return view("pages.{$page->template}", [
            'page' => $page,
            'pressPosts' => $pressPosts ?? collect(),
            'clientReferences' => $clientReferences,
            'missingTranslation' => $page->isMissingTranslation(app()->getLocale()),
        ]);
    }
}