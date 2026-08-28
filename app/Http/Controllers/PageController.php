<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $page = Page::published()->where('slug', 'home')->firstOrFail();

        return $this->render($page);
    }

    public function show(string $locale, string $slug): View
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        return $this->render($page);
    }

    protected function render(Page $page): View
    {
        return view("pages.{$page->template}", [
            'page' => $page,
            'missingTranslation' => $page->isMissingTranslation(app()->getLocale()),
        ]);
    }
}