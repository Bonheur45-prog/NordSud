<?php

namespace Tests\Feature;

use App\Models\FooterSettings;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_renders_social_links_from_footer_settings(): void
    {
        FooterSettings::create([
            'facebook_url' => 'https://www.facebook.com/testcompany',
            'instagram_url' => 'https://www.instagram.com/testcompany',
            'x_url' => null, // deliberately blank — should not render an X icon
            'linkedin_url' => 'https://www.linkedin.com/company/testcompany',
        ]);

        $response = $this->get('/fr/press');

        $response->assertOk();
        $response->assertSee('https://www.facebook.com/testcompany', false);
        $response->assertSee('https://www.instagram.com/testcompany', false);
        $response->assertSee('https://www.linkedin.com/company/testcompany', false);
    }

    public function test_footer_hides_a_social_icon_when_its_url_is_blank(): void
    {
        FooterSettings::create([
            'facebook_url' => 'https://www.facebook.com/testcompany',
            'instagram_url' => null,
            'x_url' => null,
            'linkedin_url' => null,
        ]);

        $response = $this->get('/fr/press');

        $response->assertOk();
        $response->assertSee('https://www.facebook.com/testcompany', false);
        // Only the one filled-in network should render a footer icon —
        // links() filters out blanks, so no "title" attribute for the
        // three networks left empty.
        $response->assertDontSee('title="X (Twitter)"', false);
        $response->assertDontSee('title="Instagram"', false);
        $response->assertDontSee('title="Linkedin"', false);
    }

    public function test_footer_country_phone_numbers_come_from_the_contact_page(): void
    {
        Page::create([
            'slug' => 'contact',
            'template' => 'contact',
            'status' => 'published',
            'title_fr' => 'Contact',
            'content' => [
                'telephones' => [
                    [
                        'country' => ['fr' => 'Rwanda', 'en' => 'Rwanda'],
                        'details' => ['fr' => '+250 700 000 111', 'en' => '+250 700 000 111'],
                    ],
                    [
                        'country' => ['fr' => 'Kenya', 'en' => 'Kenya'],
                        'details' => ['fr' => '+254 700 000 222 - Nairobi', 'en' => '+254 700 000 222 - Nairobi'],
                    ],
                ],
            ],
        ]);

        $response = $this->get('/fr/press');

        $response->assertOk();
        $response->assertSee('Rwanda');
        $response->assertSee('+250 700 000 111');
        $response->assertSee('Kenya');
        $response->assertSee('+254 700 000 222 - Nairobi');
    }

    public function test_footer_does_not_break_when_contact_page_or_footer_settings_are_missing(): void
    {
        // No Contact page, no FooterSettings row created — the footer
        // must still render the rest of the page without error.
        $response = $this->get('/fr/press');

        $response->assertOk();
    }

    public function test_admin_can_edit_footer_settings(): void
    {
        $this->actingAs(\App\Models\User::factory()->create());

        $this->get('/admin/footer-settings')->assertOk();
    }
}
