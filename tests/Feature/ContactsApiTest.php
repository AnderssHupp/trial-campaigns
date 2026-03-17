<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_contact(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'status' => 'active',
            ]);

        $this->assertDatabaseHas('contacts', [
            'email' => 'john@example.com',
            'status' => 'active',
        ]);
    }

    public function test_contacts_index(): void
    {
        Contact::factory()->count(15)->create();

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'per_page',
            ]);
    }

    public function test_can_unsubscribe_contact(): void
    {
        $contact = Contact::factory()->create([
            'status' => 'active',
        ]);

        $response = $this->postJson("/api/contacts/{$contact->id}/unsubscribe");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Contact unsubscribed',
            ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'status' => 'unsubscribed',
        ]);
    }
}

