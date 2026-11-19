<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\TreatmentPackage;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function bookings(Request $request): View
    {
        $month = $this->month($request);
        $start = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();
        $bookings = Booking::with(['customer', 'treatmentPackage', 'payments'])
            ->where('starts_at', '>=', $start)->where('starts_at', '<', $start->copy()->addMonth())
            ->orderBy('starts_at')->paginate(15)->withQueryString();
        $customers = Customer::orderBy('name')->get();
        $packages = TreatmentPackage::orderBy('name')->get();

        return view('bookings.index', compact('bookings', 'customers', 'packages', 'month'));
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        Booking::create($this->bookingData($request));

        return back()->with('success', 'Tempahan berjaya ditambah.');
    }

    public function updateBooking(Request $request, Booking $booking): RedirectResponse
    {
        $booking->update($this->bookingData($request));

        return back()->with('success', 'Tempahan dikemas kini.');
    }

    public function deleteBooking(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return back()->with('success', 'Tempahan dipadam.');
    }

    private function bookingData(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'treatment_package_id' => ['nullable', 'exists:treatment_packages,id'],
            'starts_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['confirmed', 'completed', 'cancelled'])],
        ]);
    }

    private function month(Request $request): string
    {
        $month = (string) $request->query('month', '2027-01');

        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month) ? $month : '2027-01';
    }
}
