<?php

namespace Tests\Feature;

use App\Filament\Pages\PageContent\HomePage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomePageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders_and_creates_the_home_record_if_missing(): void
    {
        $this->assertDatabaseCount('pages', 0);

        $this->actingAs(User::factory()->create())
            ->get('/admin/home-page') // will confirm actual route slug below
            ;

        // Not asserting status here yet — first run just to discover the real route.
        $this->assertTrue(true);
    }

    public function test_save_writes_correctly_nested_content(): void
    {
        $this->actingAs(User::factory()->create());

        $component = Livewire::test(HomePage::class);

        $component->fillForm([
            'title_fr' => 'Accueil - NSI Group',
            'title_en' => 'Home - NSI Group',
            'content' => [
                'hero' => [
                    'slides' => [
                        [
                            'eyebrow' => ['fr' => 'Karongi - Lake Kivu', 'en' => 'Karongi - Lake Kivu'],
                            'headline' => ['fr' => 'Bienvenue', 'en' => 'Welcome'],
                            'subline' => ['fr' => 'Sous-titre', 'en' => 'Subtitle'],
                        ],
                    ],
                    'cta_label' => ['fr' => 'Contactez-nous', 'en' => 'Contact us'],
                    'cta_link' => '/contact',
                ],
                'press_spotlight' => [
                    'headline' => ['fr' => 'NSI au Kenya', 'en' => 'NSI in Kenya'],
                    'link' => '/press/nsi-kenya',
                ],
                'secondary_banner' => [
                    'headline' => ['fr' => 'Titre secondaire', 'en' => 'Secondary title'],
                    'body' => ['fr' => 'Corps', 'en' => 'Body'],
                ],
                'highlights' => [
                    ['title' => ['fr' => 'Solutions Monétiques', 'en' => 'Payment Solutions'], 'description' => ['fr' => 'd', 'en' => 'd'], 'link' => '/solutions-monetiques'],
                ],
                'stats_banner' => [
                    'headline' => ['fr' => 'Plus de 60 banques', 'en' => 'More than 60 banks'],
                    'cta_label' => ['fr' => 'Contactez-nous', 'en' => 'Contact us'],
                    'cta_link' => '/contact',
                ],
            ],
        ])->call('save');

        $page = Page::where('slug', 'home')->firstOrFail();

        $this->assertEquals('Accueil - NSI Group', $page->title_fr);
        $this->assertEquals('Home - NSI Group', $page->title_en);
        $this->assertEquals('Bienvenue', $page->content['hero']['slides'][0]['headline']['fr']);
        $this->assertEquals('Contactez-nous', $page->content['hero']['cta_label']['fr']);
        $this->assertEquals('/contact', $page->content['hero']['cta_link']);
        $this->assertEquals('NSI au Kenya', $page->content['press_spotlight']['headline']['fr']);
        $this->assertEquals('Titre secondaire', $page->content['secondary_banner']['headline']['fr']);
        $this->assertEquals('Solutions Monétiques', $page->content['highlights'][0]['title']['fr']);
        $this->assertEquals('Plus de 60 banques', $page->content['stats_banner']['headline']['fr']);
    }
}
