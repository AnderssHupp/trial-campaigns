<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'status',
    ];

    protected $table = 'contacts';

    public function contactLists()
    {
        return $this->belongsToMany(ContactList::class);
    }

}
