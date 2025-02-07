<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
            'sort_by' => 'string|in:first_name,last_name,email,created_at',
            'sort_order' => 'string|in:asc,desc'
        ]);

        $perPage = $request->input('per_page', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $contacts = Auth::user()
            ->contacts()
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return response()->json($contacts);
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

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
            'per_page' => 'integer|min:1|max:100',
            'page' => 'integer|min:1'
        ]);

        $query = $request->input('query');
        $perPage = $request->input('per_page', 15);

        $contacts = Auth::user()
            ->contacts()
            ->whereFullText(['first_name', 'last_name', 'middle_name', 'email'], $query)
            ->orWhere(function ($q) use ($query) {
                $q->where('phone_number', 'LIKE', "%{$query}%")
                    ->orWhereFullText('notes', $query);
            })
            ->orderBy('first_name')
            ->paginate($perPage);

        return response()->json($contacts);
    }
}
