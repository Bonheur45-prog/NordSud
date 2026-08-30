<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(string $locale): View
    {
        $page = Page::published()->where('slug', 'contact')->firstOrFail();

        return view('pages.contact', [
            'page' => $page,
            'locale' => $locale,
        ]);
    }

    public function store(Request $request, string $locale): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:10000'],
        ]);

        ContactSubmission::create($data);

        return redirect()
            ->route('contact.show', ['locale' => $locale])
            ->with('success', 'Your message has been sent successfully.');
    }
}
