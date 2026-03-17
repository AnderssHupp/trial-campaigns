<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignDispatchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatch_creates_campaign_sends_and_updates_status(): void
    {
        $list = ContactList::factory()->create();

        $contacts = Contact::factory()->count(3)->create([
            'status' => 'active',
        ]);

        $list->contacts()->sync($contacts->pluck('id')->all());

        $campaign = Campaign::factory()->create([
            'contact_list_id' => $list->id,
            'status' => 'draft',
        ]);

        $response = $this->postJson("/api/campaigns/{$campaign->id}/dispatch");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Campaign dispatch started',
            ]);

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'status' => 'sending',
        ]);

        $this->assertDatabaseCount('campaign_sends', 3);

        $this->assertDatabaseHas('campaign_sends', [
            'campaign_id' => $campaign->id,
            'status' => 'sent',
        ]);
    }
}

