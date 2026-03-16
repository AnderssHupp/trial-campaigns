<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'status',
    ];
    protected $casts = [
        'status' => 'enum:active,unsubscribed',
    ];
    protected $table = 'contacts';

    public function contactLists()
    {
        return $this->belongsToMany(ContactList::class);
    }

}
