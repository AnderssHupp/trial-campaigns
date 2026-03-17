<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactListsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_contact_list(): void
    {
        $payload = ['name' => 'Minha Lista'];

        $response = $this->postJson('/api/contact-lists', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Minha Lista']);

        $this->assertDatabaseHas('contact_lists', ['name' => 'Minha Lista']);
    }

    public function test_contact_lists_index(): void
    {
        ContactList::factory()->count(15)->create();

        $response = $this->getJson('/api/contact-lists');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'per_page',
            ]);
    }

    public function test_can_add_contact_to_list(): void
    {
        $list = ContactList::factory()->create();
        $contact = Contact::factory()->create();

        $response = $this->postJson("/api/contact-lists/{$list->id}/contacts", [
            'contact_id' => $contact->id,
            'contact_list_id' => $list->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Contact added to list',
            ]);

        $this->assertDatabaseHas('contact_contact_list', [
            'contact_id' => $contact->id,
            'contact_list_id' => $list->id,
        ]);
    }
}

