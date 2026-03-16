<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignSend extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'campaign_id',
        'contact_id',
        'status',
    ];
    protected $casts = [
        'status' => 'string',
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
