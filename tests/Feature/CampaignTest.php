<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_campaigns_with_stats(): void
    {
        $list = ContactList::factory()->create();
        $campaign = Campaign::factory()->create([
            'contact_list_id' => $list->id,
        ]);

        $contact = Contact::factory()->create(['status' => 'active']);
        $campaign->sends()->createMany([
            ['contact_id' => $contact->id, 'status' => 'pending'],
            ['contact_id' => $contact->id, 'status' => 'sent'],
            ['contact_id' => $contact->id, 'status' => 'failed'],
        ]);

        $response = $this->getJson('/api/campaigns');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'pending_count' => 1,
                'sent_count' => 1,
                'failed_count' => 1,
            ]);
    }
}

