<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
            'status' => 'string|in:pending,confirmed,cancelled,completed',
            'start_date' => 'date_format:Y-m-d',
            'end_date' => 'date_format:Y-m-d|after_or_equal:start_date'
        ]);

        $query = Auth::user()->appointments()->with('contact:id,first_name,last_name');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('start', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $appointments = $query->orderBy('start', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json($appointments);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $appointment = Auth::user()->appointments()->create($request->validated());
        $appointment->load('contact:id,first_name,last_name');

        return response()->json(['appointment' => $appointment], 201);
    }

    public function show($id)
    {
        $appointment = Auth::user()->appointments()
            ->with('contact:id,first_name,last_name')
            ->findOrFail($id);

        return response()->json(['appointment' => $appointment]);
    }

    public function update(StoreAppointmentRequest $request, $id)
    {
        $appointment = Auth::user()->appointments()->findOrFail($id);
        $appointment->update($request->validated());
        $appointment->load('contact:id,first_name,last_name');

        return response()->json(['appointment' => $appointment]);
    }

    public function destroy($id)
    {
        $appointment = Auth::user()->appointments()->findOrFail($id);
        $appointment->delete();

        return response()->json([
            'message' => 'Appointment deleted successfully'
        ]);
    }
}
