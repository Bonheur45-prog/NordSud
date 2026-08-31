<?php

namespace Tests\Feature;

use App\Filament\Pages\PageContent\BankCardPage;
use App\Filament\Pages\PageContent\SecuredDocumentsPage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_bank_card_save_writes_correctly_nested_sections(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(BankCardPage::class)
            ->fillForm([
                'title_fr' => 'Carte Bancaire',
                'title_en' => 'Bank Card',
                'status' => 'published',
                'order' => 0,
                'content' => [
                    'introduction' => ['fr' => 'Intro FR', 'en' => 'Intro EN'],
                    'sections' => [
                        [
                            'title' => ['fr' => 'Section 1', 'en' => 'Section 1 EN'],
                            'bullets' => [
                                ['fr' => 'Point un', 'en' => 'Point one'],
                            ],
                        ],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'bank-card')->firstOrFail();

        $this->assertEquals('product', $page->template);
        $this->assertEquals('Intro FR', $page->content['introduction']['fr']);
        $this->assertEquals('Section 1', $page->content['sections'][0]['title']['fr']);
        $this->assertEquals('Point un', $page->content['sections'][0]['bullets'][0]['fr']);
    }

    public function test_secured_documents_save_writes_fixed_bands_and_closing(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(SecuredDocumentsPage::class)
            ->fillForm([
                'title_fr' => 'Documents Sécurisés',
                'status' => 'published',
                'order' => 0,
                'content' => [
                    'introduction' => ['fr' => 'Intro FR', 'en' => ''],
                    'security' => [
                        'description' => ['fr' => '<p>Desc sécurité</p>', 'en' => ''],
                        'bullets' => [['fr' => 'Mécanisme 1', 'en' => '']],
                    ],
                    'services' => [
                        'description' => ['fr' => '<p>Desc services</p>', 'en' => ''],
                        'bullets' => [['fr' => 'Service 1', 'en' => '']],
                    ],
                    'closing' => ['fr' => '<p>Clôture</p>', 'en' => ''],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'secured-documents')->firstOrFail();

        $this->assertEquals('Mécanisme 1', $page->content['security']['bullets'][0]['fr']);
        $this->assertEquals('Service 1', $page->content['services']['bullets'][0]['fr']);
        $this->assertStringContainsString('Clôture', $page->content['closing']['fr']);
    }

    public function test_bank_card_is_not_reachable_through_the_generic_product_editor(): void
    {
        // Same protection pattern as Partners — dedicated pages must be
        // excluded from the generic Resource's query, or editing through
        // it would silently overwrite the real sections/bullets structure
        // with the generic {logo, paragraphs} shape.
        $this->actingAs(User::factory()->create())
            ->get('/admin/products/'.Page::where('slug', 'bank-card')->value('id').'/edit')
            ->assertNotFound();
    }
}
