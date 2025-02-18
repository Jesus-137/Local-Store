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

        $contacts = $user->contacts()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Contact deleted successfully.',
            'contacts' => $contacts
        ], 200);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string',
            'per_page' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
            'sort_by' => 'string|in:first_name,last_name,email,created_at',
            'sort_order' => 'string|in:asc,desc'
        ]);

        $input = $request->input('query');
        $perPage = $request->input('per_page', 15);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $user = Auth::user();

        if (!$input) {
            return response()->json(
                $user->contacts()
                    ->orderBy($sortBy, $sortOrder)
                    ->paginate($perPage)
            );
        }

        $exactMatches = $user->contacts()
            ->where(function ($q) use ($input) {
                $q->where('first_name', 'LIKE', $input . '%')
                    ->orWhere('last_name', 'LIKE', $input . '%')
                    ->orWhere('middle_name', 'LIKE', $input . '%');
            })
            ->select(
                '*',
                \DB::raw('1 as priority'),
                \DB::raw("CASE
                    WHEN first_name LIKE '{$input}%' THEN 1
                    WHEN last_name LIKE '{$input}%' THEN 2
                    WHEN middle_name LIKE '{$input}%' THEN 3
                    ELSE 4
                END as match_order")
            );

        $partialMatches = $user->contacts()
            ->where(function ($q) use ($input) {
                $q->where('first_name', 'LIKE', '%' . $input . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $input . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $input . '%')
                    ->orWhere('email', 'LIKE', '%' . $input . '%')
                    ->orWhere('phone_number', 'LIKE', '%' . $input . '%')
                    ->orWhere('notes', 'LIKE', '%' . $input . '%');
            })
            ->whereNotIn('id', $exactMatches->pluck('id'))
            ->select(
                '*',
                \DB::raw('2 as priority'),
                \DB::raw('4 as match_order')
            );

        $contacts = $exactMatches
            ->union($partialMatches)
            ->orderBy('priority')
            ->orderBy('match_order')
            ->orderBy('first_name')
            ->paginate($perPage);

        return response()->json($contacts);
    }
}
