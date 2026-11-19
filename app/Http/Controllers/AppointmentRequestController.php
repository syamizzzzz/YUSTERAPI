<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\TreatmentPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentRequestController extends Controller
{
    public function appointmentRequests(): View
    {
        $requests = AppointmentRequest::with(['treatmentPackage', 'booking.customer'])->latest()->paginate(15);
        $packages = TreatmentPackage::where('is_active', true)->orderBy('name')->get();

        return view('appointment-requests.index', compact('requests', 'packages'));
    }

    public function updateAppointmentRequest(Request $request, AppointmentRequest $appointmentRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'contacted', 'closed'])],
        ]);

        if ($appointmentRequest->booking_id !== null) {
            return back()->withErrors(['status' => 'Permintaan yang telah disahkan tidak boleh ditukar status.']);
        }

        $appointmentRequest->update($data);

        return back()->with('success', 'Status permintaan dikemas kini.');
    }

    public function confirmAppointmentRequest(Request $request, AppointmentRequest $appointmentRequest): RedirectResponse
    {
        $data = $request->validate([
            'starts_at' => ['required', 'date'],
            'treatment_package_id' => ['nullable', 'exists:treatment_packages,id'],
        ]);

        $booking = DB::transaction(function () use ($appointmentRequest, $data): Booking {
            $lockedRequest = AppointmentRequest::whereKey($appointmentRequest->id)->lockForUpdate()->firstOrFail();

            if ($lockedRequest->booking_id !== null) {
                throw ValidationException::withMessages(['booking' => 'Permintaan ini sudah disahkan sebagai booking.']);
            }

            if ($lockedRequest->status === 'closed') {
                throw ValidationException::withMessages(['booking' => 'Buka semula permintaan sebelum mengesahkan booking.']);
            }

            $customer = Customer::where('name', $lockedRequest->name)->where('phone', $lockedRequest->phone)->first()
                ?? Customer::create(['name' => $lockedRequest->name, 'phone' => $lockedRequest->phone]);
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'treatment_package_id' => $data['treatment_package_id'] ?? null,
                'starts_at' => $data['starts_at'],
                'status' => 'confirmed',
            ]);
            $lockedRequest->update(['booking_id' => $booking->id, 'status' => 'confirmed']);

            return $booking;
        });

        return redirect()->route('customers.show', $booking->customer_id)
            ->with('success', 'Permintaan disahkan. Pelanggan dan booking berjaya disimpan.');
    }
}
