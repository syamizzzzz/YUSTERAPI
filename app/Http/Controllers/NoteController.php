<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\CustomerNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NoteController extends Controller
{
    public function notes(): View
    {
        $notes = CustomerNote::with(['customer', 'booking'])->latest()->paginate(15);
        $customers = Customer::orderBy('name')->get();
        $bookings = Booking::with('customer')->latest('starts_at')->limit(200)->get();

        return view('notes.index', compact('notes', 'customers', 'bookings'));
    }

    public function storeNote(Request $request): RedirectResponse
    {
        CustomerNote::create($this->noteData($request));

        return back()->with('success', 'Catatan berjaya ditambah.');
    }

    public function updateNote(Request $request, CustomerNote $customerNote): RedirectResponse
    {
        $customerNote->update($this->noteData($request));

        return back()->with('success', 'Catatan dikemas kini.');
    }

    public function deleteNote(CustomerNote $customerNote): RedirectResponse
    {
        $customerNote->delete();

        return back()->with('success', 'Catatan dipadam.');
    }

    private function noteData(Request $request): array
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        if (! empty($data['booking_id']) && ! Booking::whereKey($data['booking_id'])->where('customer_id', $data['customer_id'])->exists()) {
            throw ValidationException::withMessages(['booking_id' => 'Tempahan ini bukan milik pelanggan yang dipilih.']);
        }

        return $data;
    }
}
