<?php

namespace Tests\Feature;

use App\Filament\Pages\PageContent\GroupJoosPage;
use App\Filament\Resources\Partners\PartnerResource;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerEditorConflictTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This is the exact bug: Group JOOS has rich content (certificates,
     * production sites, capacity) saved through its dedicated page. If the
     * generic PartnerResource could still reach that row, opening it and
     * hitting Save would replace `content` wholesale with the generic
     * shape (just logo + paragraphs), destroying everything else. This
     * proves that can no longer happen — the row isn't even reachable
     * through the generic resource's edit route.
     */
    public function test_group_joos_is_not_reachable_through_the_generic_partner_editor(): void
    {
        $joos = Page::create([
            'slug' => 'group-joos',
            'template' => 'partner',
            'status' => 'published',
            'title_fr' => 'Group JOOS',
            'content' => [
                'hero' => ['image' => 'joos-hero.jpg'],
                'certificates' => ['title' => ['fr' => 'Certifications'], 'items' => [['fr' => 'ISO 14298']]],
                'production_sites' => [['name' => ['fr' => 'Turnhout']]],
            ],
        ]);

        $this->actingAs(User::factory()->create())
            ->get("/admin/partners/{$joos->id}/edit")
            ->assertNotFound();
    }

    /**
     * The dedicated editor must still work and must not have lost any of
     * its route registration in the process of fixing the conflict.
     */
    public function test_group_joos_is_still_editable_through_its_dedicated_page(): void
    {
        Page::create([
            'slug' => 'group-joos',
            'template' => 'partner',
            'status' => 'published',
            'title_fr' => 'Group JOOS',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(GroupJoosPage::getUrl())
            ->assertOk()
            ->assertSee('Group JOOS');
    }

    /**
     * A brand new partner (no dedicated page exists for it) must still be
     * fully manageable through the generic resource — the fix must not
     * have broken the "+ Add New Partner" flow for future partners.
     */
    public function test_a_new_partner_without_a_dedicated_page_is_still_editable_generically(): void
    {
        $newPartner = Page::create([
            'slug' => 'some-future-partner',
            'template' => 'partner',
            'status' => 'published',
            'title_fr' => 'Some Future Partner',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(PartnerResource::getUrl('edit', ['record' => $newPartner]))
            ->assertOk()
            ->assertSee('Some Future Partner');
    }

    public function test_the_partner_create_page_still_works(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(PartnerResource::getUrl('create'))
            ->assertOk();
    }

    /**
     * The sidebar must show every partner as its own direct link, routed
     * to the CORRECT editor for each — dedicated ones to their dedicated
     * page, new ones to the generic resource.
     *
     * This calls the nav-building method directly after seeding, rather
     * than going through a real HTTP request — because within a single
     * test process, the panel provider boots (and builds nav) BEFORE
     * RefreshDatabase finishes migrating, so data created later in the
     * test body would never be reflected in a subsequent HTTP call
     * within that same test. That ordering quirk is specific to how
     * PHPUnit reuses one booted app per test; it doesn't happen in real
     * request handling, where the database is already migrated before
     * any request arrives. Testing the method directly avoids relying
     * on that timing and is a more precise test of the actual logic.
     */
    public function test_sidebar_routes_each_partner_to_its_correct_editor(): void
    {
        Page::create(['slug' => 'group-joos', 'template' => 'partner', 'status' => 'published', 'title_fr' => 'Group JOOS']);
        $futurePartner = Page::create(['slug' => 'some-future-partner', 'template' => 'partner', 'status' => 'published', 'title_fr' => 'Some Future Partner']);

        $items = (new \App\Providers\Filament\AdminPanelProvider(app()))->buildDynamicNavigationItems();
        $labels = array_map(fn ($item) => $item->getLabel(), $items);

        $this->assertContains('Group JOOS', $labels);
        $this->assertContains('Some Future Partner', $labels);
        $this->assertContains('Add New Partner', $labels);

        $joosItem = collect($items)->firstWhere(fn ($item) => $item->getLabel() === 'Group JOOS');
        $futureItem = collect($items)->firstWhere(fn ($item) => $item->getLabel() === 'Some Future Partner');

        // Group JOOS must route to its dedicated page, NOT the generic resource.
        $this->assertEquals(GroupJoosPage::getUrl(), $joosItem->getUrl());
        // A partner without a dedicated page must route to the generic editor.
        $this->assertEquals(
            PartnerResource::getUrl('edit', ['record' => $futurePartner]),
            $futureItem->getUrl()
        );
    }
}
