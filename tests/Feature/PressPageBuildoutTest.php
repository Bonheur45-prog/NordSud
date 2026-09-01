<?php

namespace Tests\Feature;

use App\Models\PressAuthor;
use App\Models\PressComment;
use App\Models\PressPost;
use App\Models\PressTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PressPageBuildoutTest extends TestCase
{
    use RefreshDatabase;

    protected function makePost(array $overrides = []): PressPost
    {
        $post = PressPost::create(array_merge([
            'slug' => 'test-post-'.uniqid(),
            'type' => 'press',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ], $overrides));

        $post->translations()->create([
            'locale' => 'fr',
            'title' => 'Titre de test',
            'excerpt' => 'Extrait',
            'body' => '<p>Corps de larticle de test.</p>',
        ]);

        return $post;
    }

    public function test_index_search_filters_by_title(): void
    {
        $match = $this->makePost();
        $match->translations()->first()->update(['title' => 'Comprendre la conformité EMV']);

        $other = $this->makePost();
        $other->translations()->first()->update(['title' => 'Un tout autre sujet']);

        $response = $this->get('/fr/press?q=EMV');

        $response->assertOk();
        $response->assertSee('Comprendre la conformité EMV');

        // Same reasoning as the tag-filter test: the sidebar legitimately
        // shows recent posts site-wide regardless of the active search.
        $mainContent = str($response->getContent())
            ->before('press-sidebar')
            ->toString();

        $this->assertStringContainsString('Comprendre la conformité EMV', $mainContent);
        $this->assertStringNotContainsString('Un tout autre sujet', $mainContent);
    }

    public function test_index_tag_filter_only_shows_tagged_posts(): void
    {
        $tag = PressTag::create(['name_fr' => 'Sécurité', 'name_en' => 'Security', 'slug' => 'security']);
        $otherTag = PressTag::create(['name_fr' => 'Paiements', 'name_en' => 'Payments', 'slug' => 'payments']);

        $tagged = $this->makePost();
        $tagged->tags()->sync([$tag->id]);
        $tagged->translations()->first()->update(['title' => 'Article Sécurité']);

        $untagged = $this->makePost();
        $untagged->tags()->sync([$otherTag->id]);
        $untagged->translations()->first()->update(['title' => 'Article Paiements']);

        $response = $this->get('/fr/press?tag=security');
        $response->assertOk();

        // The sidebar legitimately lists posts/tags site-wide regardless of
        // the active filter, so scope the "excluded" assertion to the main
        // article grid only, not the whole page.
        $mainContent = str($response->getContent())
            ->before('press-sidebar')
            ->toString();

        $this->assertStringContainsString('Article Sécurité', $mainContent);
        $this->assertStringNotContainsString('Article Paiements', $mainContent);
    }

    public function test_featured_posts_appear_in_popular_posts_sidebar(): void
    {
        \App\Models\TranslationString::create([
            'key' => 'press.popular_posts',
            'group' => 'press',
            'fr' => 'Articles Populaires',
            'en' => 'Popular Posts',
        ]);

        $featured = $this->makePost(['is_featured' => true]);
        $featured->translations()->first()->update(['title' => 'Article Vedette']);

        $response = $this->get('/fr/press');

        $response->assertOk();
        $response->assertSee('Articles Populaires');
        $response->assertSee('Article Vedette');
    }

    public function test_show_page_displays_author_tags_and_approved_comments_only(): void
    {
        $author = PressAuthor::create([
            'name' => 'Diane Uwimana',
            'role_fr' => 'Responsable Sécurité',
            'role_en' => 'Security Lead',
        ]);
        $tag = PressTag::create(['name_fr' => 'Sécurité', 'name_en' => 'Security', 'slug' => 'security']);

        $post = $this->makePost(['author_id' => $author->id]);
        $post->tags()->sync([$tag->id]);

        PressComment::create([
            'press_post_id' => $post->id,
            'author_name' => 'Visible Commenter',
            'body' => 'This comment is approved.',
            'approved' => true,
        ]);
        PressComment::create([
            'press_post_id' => $post->id,
            'author_name' => 'Hidden Commenter',
            'body' => 'This comment is not yet approved.',
            'approved' => false,
        ]);

        $response = $this->get('/fr/press/'.$post->slug);

        $response->assertOk();
        $response->assertSee('Diane Uwimana');
        $response->assertSee('Sécurité');
        $response->assertSee('Visible Commenter');
        $response->assertDontSee('Hidden Commenter');
    }

    public function test_previous_and_next_navigation_link_to_neighboring_posts(): void
    {
        $older = $this->makePost(['published_at' => now()->subDays(3)]);
        $older->translations()->first()->update(['title' => 'Article Plus Ancien']);

        $current = $this->makePost(['published_at' => now()->subDays(2)]);

        $newer = $this->makePost(['published_at' => now()->subDay()]);
        $newer->translations()->first()->update(['title' => 'Article Plus Récent']);

        $response = $this->get('/fr/press/'.$current->slug);

        $response->assertOk();
        $response->assertSee('Article Plus Ancien');
        $response->assertSee('Article Plus Récent');
    }

    public function test_public_comment_submission_is_held_for_moderation(): void
    {
        $post = $this->makePost();

        $response = $this->from('/fr/press/'.$post->slug)->post(
            "/fr/press/{$post->slug}/comments",
            [
                'author_name' => 'Jean Client',
                'body' => 'Merci pour cet article très utile.',
                'website' => '', // honeypot left blank, like a real visitor
            ]
        );

        $response->assertRedirect('/fr/press/'.$post->slug.'#comments');

        $comment = PressComment::where('author_name', 'Jean Client')->first();
        $this->assertNotNull($comment);
        $this->assertFalse($comment->approved);

        // Not visible on the public page until approved.
        $this->get('/fr/press/'.$post->slug)->assertDontSee('Merci pour cet article très utile.');

        $comment->update(['approved' => true]);
        $this->get('/fr/press/'.$post->slug)->assertSee('Merci pour cet article très utile.');
    }

    public function test_comment_honeypot_rejects_bot_submissions(): void
    {
        $post = $this->makePost();

        $this->from('/fr/press/'.$post->slug)->post(
            "/fr/press/{$post->slug}/comments",
            [
                'author_name' => 'Spam Bot',
                'body' => 'buy cheap watches',
                'website' => 'http://spam.example.com', // bots fill every field
            ]
        );

        $this->assertSame(0, PressComment::where('author_name', 'Spam Bot')->count());
    }

    public function test_comment_requires_name_and_body(): void
    {
        $post = $this->makePost();

        $response = $this->from('/fr/press/'.$post->slug)->post(
            "/fr/press/{$post->slug}/comments",
            ['author_name' => '', 'body' => '']
        );

        $response->assertSessionHasErrors(['author_name', 'body']);
        $this->assertSame(0, PressComment::count());
    }

    public function test_admin_can_manage_press_authors_and_tags(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/press-authors')->assertOk();
        $this->get('/admin/press-authors/create')->assertOk();
        $this->get('/admin/press-tags')->assertOk();
        $this->get('/admin/press-tags/create')->assertOk();
    }

    public function test_admin_can_open_press_post_editor_with_comments_relation_manager(): void
    {
        $post = $this->makePost();
        $this->actingAs(User::factory()->create());

        $this->get('/admin/press-posts')->assertOk();
        $this->get("/admin/press-posts/{$post->id}/edit")->assertOk();
    }

    public function test_locale_carbon_formatting_is_set_from_the_route(): void
    {
        $post = $this->makePost(['published_at' => '2026-07-22 09:00:00']);

        $response = $this->get('/fr/press/'.$post->slug);
        $response->assertOk();
        $response->assertSee('juillet');

        $response = $this->get('/en/press/'.$post->slug);
        $response->assertOk();
        $response->assertSee('July');
    }
}
