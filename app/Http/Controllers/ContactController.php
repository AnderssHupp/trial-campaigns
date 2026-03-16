<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Contact::paginate(10));
    }

    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());

        return response()->json($contact, 201);
    }

    public function unsubscribe($id)
    {
        $contact = Contact::findOrFail($id);

        $contact->update([
            'status' => 'unsubscribed'
        ]);

        return response()->json(['message' => 'Contact unsubscribed']);
    }
}
