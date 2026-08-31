<?php

namespace Tests\Feature;

use App\Filament\Pages\PageContent\SoftwareExpertisePage;
use App\Filament\Pages\PageContent\SolutionsMonetiquePage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SolutionPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_solutions_monetiques_save_writes_sections(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(SolutionsMonetiquePage::class)
            ->fillForm([
                'title_fr' => 'Solutions Monétiques',
                'status' => 'published',
                'order' => 0,
                'content' => [
                    'introduction' => ['fr' => 'Intro FR', 'en' => ''],
                    'sections' => [
                        [
                            'title' => ['fr' => 'Impression de Cartes', 'en' => ''],
                            'bullets' => [['fr' => 'Personnalisation', 'en' => '']],
                        ],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'solutions-monetiques')->firstOrFail();

        $this->assertEquals('solution', $page->template);
        $this->assertEquals('Impression de Cartes', $page->content['sections'][0]['title']['fr']);
        $this->assertEquals('Personnalisation', $page->content['sections'][0]['bullets'][0]['fr']);
    }

    public function test_software_expertise_save_writes_checklist_points(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(SoftwareExpertisePage::class)
            ->fillForm([
                'title_fr' => 'Expertise Logiciel',
                'status' => 'published',
                'order' => 0,
                'content' => [
                    'introduction' => [
                        'heading' => ['fr' => 'Titre principal', 'en' => ''],
                        'text' => ['fr' => '<p>Texte intro</p>', 'en' => ''],
                    ],
                    'points' => [
                        ['fr' => 'Capacité 1', 'en' => ''],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'software-expertise')->firstOrFail();

        $this->assertEquals('Titre principal', $page->content['introduction']['heading']['fr']);
        $this->assertEquals('Capacité 1', $page->content['points'][0]['fr']);
    }
}
