<?php

namespace Tests\Feature;

use App\Models\ClientReference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientReferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_references_are_returned_in_display_order(): void
    {
        ClientReference::create(['name' => 'Second Bank', 'order' => 20]);
        ClientReference::create(['name' => 'First Bank', 'order' => 10]);

        $references = ClientReference::ordered()->get();

        $this->assertSame(['First Bank', 'Second Bank'], $references->pluck('name')->all());
    }
}
