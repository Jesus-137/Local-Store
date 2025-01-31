<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $contacts = Contact::where('user_id', $user->id)->get();
        return response()->json(['contacts' => $contacts], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:contacts',
            'phone_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:20',
            'state' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $contact = Contact::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'phone_code' => $request->phone_code,
            'phone_number' => $request->phone_number,
            'state' => $request->state,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'notes' => $request->notes,
        ]);

        return response()->json(['contact' => $contact], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $contact = Contact::where('user_id', $user->id)->findOrFail($id);
        return response()->json(['contact' => $contact], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:contacts,email,' . $id,
            'phone_code' => 'sometimes|required|string|max:10',
            'phone_number' => 'sometimes|required|string|max:20',
            'state' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'birth_date' => 'sometimes|required|date',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $contact = Contact::where('user_id', $user->id)->findOrFail($id);
        $contact->update($request->all());

        return response()->json(['contact' => $contact], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $contact = Contact::where('user_id', $user->id)->findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully.'], 200);
    }
}
