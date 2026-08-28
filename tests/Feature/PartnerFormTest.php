<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_simple_partner_like_kona_i(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(\App\Filament\Resources\Partners\Pages\CreatePartner::class)
            ->fillForm([
                'title_fr' => 'KONA I',
                'title_en' => 'KONA I',
                'slug' => 'kona-i',
                'status' => 'published',
                'order' => 0,
                'content' => [
                    'paragraphs' => [
                        ['fr' => 'Kona I est une société technologique.', 'en' => 'Kona I is a technology company.'],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', [
            'slug' => 'kona-i',
            'title_fr' => 'KONA I',
        ]);

        $partner = Page::where('slug', 'kona-i')->first();
        $this->assertEquals('Kona I est une société technologique.', $partner->content['paragraphs'][0]['fr']);
    }

    public function test_can_create_a_rich_partner_like_group_joos(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(\App\Filament\Resources\Partners\Pages\CreatePartner::class)
            ->fillForm([
                'title_fr' => 'Group JOOS',
                'title_en' => 'Group JOOS',
                'slug' => 'group-joos',
                'status' => 'published',
                'order' => 0,
                'content' => [
                    'paragraphs' => [
                        ['fr' => 'Group JOOS est le partenaire de référence.', 'en' => 'Group JOOS is the reference partner.'],
                        ['fr' => 'Une structure familiale.', 'en' => 'A family-owned structure.'],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $partner = Page::where('slug', 'group-joos')->first();
        $this->assertCount(2, $partner->content['paragraphs']);
        $this->assertEquals('Une structure familiale.', $partner->content['paragraphs'][1]['fr']);
    }
}
