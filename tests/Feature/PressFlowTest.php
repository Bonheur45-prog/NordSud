<?php

namespace Tests\Feature;

use App\Models\PressPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PressFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_press_posts_are_listed_and_rendered_with_fallback_translation(): void
    {
        $post = PressPost::create([
            'slug' => 'secure-documents-launch',
            'type' => 'press',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $post->translations()->create([
            'locale' => 'fr',
            'title' => 'Lancement des documents sécurisés',
            'excerpt' => 'Une nouvelle solution.',
            'body' => '<p>Présentation de la solution.</p>',
        ]);

        $this->get('/fr/press')
            ->assertOk()
            ->assertSee('Lancement des documents sécurisés');

        $this->get('/en/press/secure-documents-launch')
            ->assertOk()
            ->assertSee('Lancement des documents sécurisés')
            ->assertSee('Présentation de la solution.', false);
    }

    public function test_future_press_posts_are_not_public(): void
    {
        $post = PressPost::create([
            'slug' => 'scheduled-announcement',
            'type' => 'press',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $post->translations()->create([
            'locale' => 'fr',
            'title' => 'Annonce programmée',
            'body' => '<p>Pas encore visible.</p>',
        ]);

        $this->get('/fr/press')
            ->assertOk()
            ->assertDontSee('Annonce programmée');
    }
}
