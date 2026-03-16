<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactList extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    protected $table = 'contact_lists';

    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
