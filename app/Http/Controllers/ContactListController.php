<?php

namespace App\Http\Controllers;

use App\Models\ContactList;
use App\Http\Requests\AddContactListRequest;
use App\Http\Requests\StoreContactListRequest;

class ContactListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ContactList::paginate(10));
    }

    public function store(StoreContactListRequest $request)
    {
        $newContactList = ContactList::create([
            'name' => $request->name
        ]);

        return response()->json($newContactList, 201);
    }

    public function addContact($id, AddContactListRequest $request)
    {
        $list = ContactList::findOrFail($id);

        $list->contacts()->syncWithoutDetaching([
            $request->contact_id
        ]);

        return response()->json([
            'message' => 'Contact added to list'
        ], 201);
    }
}
