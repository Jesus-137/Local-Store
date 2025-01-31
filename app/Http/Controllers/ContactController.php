<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $contacts = $user->contacts;
        return response()->json(['contacts' => $contacts], 200);
    }

    public function store(StoreContactRequest $request)
    {
        $user = Auth::user();
        $contact = $user->contacts()->create($request->validated());

        return response()->json(['contact' => $contact], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $contact = $user->contacts()->findOrFail($id);
        return response()->json(['contact' => $contact], 200);
    }

    public function update(UpdateContactRequest $request, $id)
    {
        $user = Auth::user();
        $contact = $user->contacts()->findOrFail($id);
        $contact->update($request->validated());

        return response()->json(['contact' => $contact], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $contact = $user->contacts()->findOrFail($id);
        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully.',
            'contacts' => $user->contacts
        ], 200);
    }
}
