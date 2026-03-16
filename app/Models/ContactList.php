<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactList extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $table = 'contact_lists';

    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
