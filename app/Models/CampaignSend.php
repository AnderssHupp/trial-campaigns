<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignSend extends Model
{
    protected $fillable = [
        'campaign_id',
        'contact_id',
        'status',
    ];
    protected $casts = [
        'status' => 'enum:pending,sent,failed',
    ];
    protected $table = 'campaign_sends';
    
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
