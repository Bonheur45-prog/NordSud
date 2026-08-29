<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression test for a real incident: when AdminPanelProvider was rewritten
 * to fix a PSR-4 autoload bug, the fix only reconstructed the dynamic
 * Partners/Products navigation — it silently dropped Home Page, Contact
 * Page, and the entire About/Solutions groups, because those pages had
 * `shouldRegisterNavigation = false` set (expecting a dynamic injector that
 * turned out not to exist for them). This locks in the real fix: those
 * pages register their OWN navigation automatically (no dynamic injector
 * needed, since About/Solutions/Home/Contact are fixed, non-growable pages)
 * and must always appear.
 */
class FullSidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_fixed_page_appears_in_the_navigation(): void
    {
        $manager = app(\Filament\Navigation\NavigationManager::class);

        // Force navigation to build by visiting a page first.
        $this->actingAs(User::factory()->create())->get('/admin');

        $labels = collect($manager->getNavigationItems())
            ->map(fn ($item) => $item->getLabel())
            ->all();

        $expected = [
            'Home Page',
            'Contact par pays',
            'NSI Group',
            'References',
            'Kinshasa Production Center',
            'Solutions Monétiques',
            'Software Expertise',
            'Consulting & Advisory',
        ];

        foreach ($expected as $label) {
            $this->assertContains(
                $label,
                $labels,
                "Expected \"{$label}\" to appear in the admin sidebar, but it didn't. ".
                'This is the exact regression from the AdminPanelProvider rewrite incident — '.
                'check shouldRegisterNavigation on that page\'s class.'
            );
        }
    }
}
