<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function payments(): View
    {
        $payments = Payment::with(['booking.customer', 'booking.treatmentPackage'])->latest()->paginate(15);
        $bookings = Booking::with('customer')->latest('starts_at')->limit(200)->get();

        return view('payments.index', compact('payments', 'bookings'));
    }

    public function storePayment(Request $request): RedirectResponse
    {
        Payment::create($this->paymentData($request));

        return back()->with('success', 'Bayaran berjaya direkodkan.');
    }

    public function updatePayment(Request $request, Payment $payment): RedirectResponse
    {
        $payment->update($this->paymentData($request));

        return back()->with('success', 'Bayaran dikemas kini.');
    }

    public function deletePayment(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return back()->with('success', 'Bayaran dipadam.');
    }

    private function paymentData(Request $request): array
    {
        $data = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'method' => ['required', Rule::in(['Tunai', 'Kad', 'Pemindahan', 'E-dompet'])],
            'status' => ['required', Rule::in(['paid', 'pending', 'refunded'])],
            'reference' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
        ]);

        if ($data['status'] === 'paid' && empty($data['paid_at'])) {
            $data['paid_at'] = now()->toDateString();
        }

        return $data;
    }
}
