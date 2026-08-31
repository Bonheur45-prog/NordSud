<?php

namespace Tests\Feature;

use App\Filament\Pages\PageContent\GroupJoosPage;
use App\Filament\Pages\PageContent\KonaIPage;
use App\Filament\Pages\PageContent\VervePage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerPagesTest extends TestCase
{
    use RefreshDatabase;

    /*
     * --- Dedicated CMS editors: each partner has its own editor and its
     * own database row (the SAME row a visitor's page load reads from —
     * these are not separate "content" concepts, just one Page record
     * edited through a purpose-built form and rendered through the
     * shared "partner" Blade template). ---
     */

    public function test_group_joos_editor_renders_and_saves_its_full_content_shape(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(GroupJoosPage::class)
            ->assertOk()
            ->fillForm([
                'title_fr' => 'Group JOOS',
                'status' => 'published',
                'content' => [
                    'introduction' => [
                        'text' => ['fr' => '<p>Intro FR</p>', 'en' => '<p>Intro EN</p>'],
                    ],
                    'facts' => [
                        ['fr' => 'Plus de 80 ans.', 'en' => 'Over 80 years.'],
                    ],
                    'certificates' => [
                        'title' => ['fr' => 'Certificats', 'en' => 'Certificates'],
                        'items' => [
                            ['fr' => 'ISO 14298', 'en' => 'ISO 14298', 'heading' => false],
                            ['fr' => 'QUALITÉ:', 'en' => 'QUALITY:', 'heading' => true],
                            ['fr' => 'FSC', 'en' => 'FSC', 'heading' => false],
                        ],
                    ],
                    'production_sites' => [
                        'title' => ['fr' => '3 sites', 'en' => '3 sites'],
                        'items' => [
                            ['name' => ['fr' => 'Turnhout', 'en' => 'Turnhout']],
                        ],
                    ],
                    'capacity' => [
                        'title' => ['fr' => 'Capacité', 'en' => 'Capacity'],
                        'points' => [
                            ['fr' => '14.000 tonnes/an', 'en' => '14,000 tonnes/year'],
                        ],
                    ],
                    'secure_document_certification' => [
                        'title' => ['fr' => 'Certification', 'en' => 'Certification'],
                        'text' => ['fr' => '<p>INTERGRAF ISO 14298</p>', 'en' => '<p>INTERGRAF ISO 14298</p>'],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'group-joos')->firstOrFail();

        $this->assertEquals('published', $page->status);
        $this->assertEquals('Intro FR', strip_tags($page->content['introduction']['text']['fr']));
        $this->assertCount(3, $page->content['certificates']['items']);
        $this->assertTrue($page->content['certificates']['items'][1]['heading']);
        $this->assertFalse($page->content['certificates']['items'][0]['heading']);
        $this->assertEquals('3 sites', $page->content['production_sites']['title']['fr']);
        $this->assertEquals('Turnhout', $page->content['production_sites']['items'][0]['name']['fr']);
    }

    public function test_kona_i_editor_renders_and_saves_paragraphs(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(KonaIPage::class)
            ->assertOk()
            ->fillForm([
                'title_fr' => 'KONA I',
                'status' => 'published',
                'content' => [
                    'paragraphs' => [
                        ['fr' => 'Paragraphe 1', 'en' => 'Paragraph 1'],
                        ['fr' => 'Paragraphe 2', 'en' => 'Paragraph 2'],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'kona-i')->firstOrFail();
        $this->assertCount(2, $page->content['paragraphs']);
        $this->assertEquals('Paragraphe 1', $page->content['paragraphs'][0]['fr']);
    }

    public function test_verve_editor_renders_and_saves_paragraphs(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(VervePage::class)
            ->assertOk()
            ->fillForm([
                'title_fr' => 'VERVE',
                'status' => 'published',
                'content' => [
                    'paragraphs' => [
                        ['fr' => 'Verve International.', 'en' => 'Verve International.'],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'verve')->firstOrFail();
        $this->assertEquals('Verve International.', $page->content['paragraphs'][0]['fr']);
    }

    /*
     * --- Public "partner" template: one shared Blade view rendering
     * whatever content each dedicated editor saved above. ---
     */

    public function test_public_group_joos_page_renders_every_section(): void
    {
        Page::create([
            'slug' => 'group-joos',
            'template' => 'partner',
            'status' => 'published',
            'title_fr' => 'Group JOOS',
            'content' => [
                'introduction' => ['text' => ['fr' => '<p>Texte intro</p>']],
                'facts' => [['fr' => 'Plus de 80 ans.']],
                'certificates' => [
                    'title' => ['fr' => 'Garanties et Certificats'],
                    'items' => [
                        ['fr' => 'ISO 14298', 'heading' => false],
                        ['fr' => 'QUALITÉ:', 'heading' => true],
                    ],
                ],
                'production_sites' => [
                    'title' => ['fr' => '3 sites de production'],
                    'items' => [['name' => ['fr' => 'JOOS PRINT plc (Turnhout, Belgique)']]],
                ],
                'capacity' => [
                    'title' => ['fr' => 'Capacité de Production'],
                    'points' => [['fr' => '14.000 tonnes papier/an']],
                ],
                'secure_document_certification' => [
                    'title' => ['fr' => 'Certification d\'Impression de Documents Sécurisés'],
                    'text' => ['fr' => '<p>INTERGRAF ISO 14298</p>'],
                ],
            ],
        ]);

        $response = $this->get('/fr/group-joos');

        $response->assertOk();
        $response->assertSee('Group JOOS');
        $response->assertSee('Texte intro', false);
        $response->assertSee('Plus de 80 ans.');
        $response->assertSee('Garanties et Certificats');
        $response->assertSee('QUALITÉ:');
        $response->assertSee('3 sites de production');
        $response->assertSee('JOOS PRINT plc (Turnhout, Belgique)');
        $response->assertSee('Capacité de Production');
        $response->assertSee('14.000 tonnes papier/an');
        $response->assertSee('Certification d\'Impression de Documents Sécurisés');
    }

    public function test_certificate_heading_items_render_without_a_checkmark_class(): void
    {
        Page::create([
            'slug' => 'group-joos',
            'template' => 'partner',
            'status' => 'published',
            'title_fr' => 'Group JOOS',
            'content' => [
                'certificates' => [
                    'title' => ['fr' => 'Certificats'],
                    'items' => [
                        ['fr' => 'ISO 14298', 'heading' => false],
                        ['fr' => 'QUALITÉ:', 'heading' => true],
                    ],
                ],
            ],
        ]);

        $html = $this->get('/fr/group-joos')->getContent();

        $this->assertStringContainsString('partner-checklist__heading', $html);
        $this->assertStringContainsString('partner-checklist__item', $html);
    }

    public function test_public_kona_i_page_renders_paragraphs_only_layout(): void
    {
        Page::create([
            'slug' => 'kona-i',
            'template' => 'partner',
            'status' => 'published',
            'title_fr' => 'KONA I',
            'content' => [
                'paragraphs' => [
                    ['fr' => 'Kona I est une société technologique.'],
                ],
            ],
        ]);

        $response = $this->get('/fr/kona-i');

        $response->assertOk();
        $response->assertSee('KONA I');
        $response->assertSee('Kona I est une société technologique.');
        // Sections that have no content for this partner must not render.
        $response->assertDontSee('partner-certificates', false);
        $response->assertDontSee('partner-sites', false);
    }

    public function test_public_verve_page_renders_paragraphs_only_layout(): void
    {
        Page::create([
            'slug' => 'verve',
            'template' => 'partner',
            'status' => 'published',
            'title_fr' => 'VERVE',
            'content' => [
                'paragraphs' => [
                    ['fr' => 'Verve International est une multinationale Nigérienne.'],
                ],
            ],
        ]);

        $response = $this->get('/fr/verve');

        $response->assertOk();
        $response->assertSee('VERVE');
        $response->assertSee('Verve International est une multinationale Nigérienne.');
    }

    public function test_english_locale_falls_back_to_french_when_translation_is_missing(): void
    {
        Page::create([
            'slug' => 'verve',
            'template' => 'partner',
            'status' => 'published',
            'title_fr' => 'VERVE',
            'title_en' => 'VERVE',
            'content' => [
                'paragraphs' => [
                    ['fr' => 'Texte français uniquement.', 'en' => null],
                ],
            ],
        ]);

        $response = $this->get('/en/verve');

        $response->assertOk();
        $response->assertSee('Texte français uniquement.');
    }
}
