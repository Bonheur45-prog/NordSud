<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_stores_a_submission(): void
    {
        $response = $this->post('/fr/contact', [
            'name' => 'Aline Mukendi',
            'email' => 'aline@example.com',
            'phone' => '+243 000 000 000',
            'company' => 'Example Bank',
            'country' => 'RD Congo',
            'message' => 'Please contact me about secure documents.',
        ]);

        $response
            ->assertRedirect('/fr/contact')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'aline@example.com',
            'country' => 'RD Congo',
            'handled' => false,
        ]);
    }

    public function test_contact_form_requires_name_email_and_message(): void
    {
        $response = $this->from('/fr/contact')->post('/fr/contact', []);

        $response
            ->assertRedirect('/fr/contact')
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_submissions', 0);
    }
}
