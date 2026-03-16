<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContactList;
use App\Models\CampaignSend;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'body', 'contact_list_id', 'status', 'scheduled_at'];

    
    protected $casts = [
        'status' => 'string',
    ];

    public function contactList(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ContactList::class);
    }

    public function sends(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignSend::class);
    }

    public function getStatsAttribute(): array
    {
        $countsByStatus = $this->sends()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $pending = (int) ($countsByStatus['pending'] ?? 0);
        $sent    = (int) ($countsByStatus['sent'] ?? 0);
        $failed  = (int) ($countsByStatus['failed'] ?? 0);

        return [
            'pending' => $pending,
            'sent'    => $sent,
            'failed'  => $failed,
            'total'   => $pending + $sent + $failed,
        ];
    }
}
