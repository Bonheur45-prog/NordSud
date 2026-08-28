<?php

namespace Tests\Feature;

use App\Models\Partner;
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
                'name_fr' => 'KONA I',
                'name_en' => 'KONA I',
                'slug' => 'kona-i',
                'status' => 'published',
                'content' => [
                    'intro' => [
                        ['fr' => 'Kona I est une société technologique.', 'en' => 'Kona I is a technology company.'],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('partners', [
            'slug' => 'kona-i',
            'name_fr' => 'KONA I',
        ]);

        $partner = Partner::where('slug', 'kona-i')->first();
        $this->assertEquals('Kona I est une société technologique.', $partner->content['intro'][0]['fr']);
    }

    public function test_can_create_a_rich_partner_like_group_joos(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(\App\Filament\Resources\Partners\Pages\CreatePartner::class)
            ->fillForm([
                'name_fr' => 'Group JOOS',
                'name_en' => 'Group JOOS',
                'slug' => 'group-joos',
                'status' => 'published',
                'content' => [
                    'intro' => [
                        ['fr' => 'Group JOOS est le partenaire de référence.', 'en' => 'Group JOOS is the reference partner.'],
                    ],
                    'facts' => [
                        ['fr' => 'Plus de 80 ans d\'expérience.', 'en' => 'More than 80 years of experience.'],
                        ['fr' => 'Structure familiale.', 'en' => 'Family-owned structure.'],
                    ],
                    'stats' => [
                        ['fr' => 'Capacité: 3 millions de pages/jour', 'en' => 'Capacity: 3 million pages/day'],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $partner = Partner::where('slug', 'group-joos')->first();
        $this->assertCount(2, $partner->content['facts']);
        $this->assertEquals('Structure familiale.', $partner->content['facts'][1]['fr']);
    }
}
