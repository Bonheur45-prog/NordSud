<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBootTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticatedUser(): User
    {
        return User::factory()->create();
    }

    public function test_partners_index_page_renders(): void
    {
        $this->actingAs($this->authenticatedUser())
            ->get('/admin/partners')
            ->assertOk();
    }

    public function test_glossary_terms_index_renders(): void
    {
        $this->actingAs($this->authenticatedUser())
            ->get('/admin/glossary-terms')
            ->assertOk();
    }

    public function test_partners_create_page_renders(): void
    {
        $this->actingAs($this->authenticatedUser())
            ->get('/admin/partners/create')
            ->assertOk();
    }
}
