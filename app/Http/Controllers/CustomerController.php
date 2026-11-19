<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function customers(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $customers = Customer::query()->when($search !== '', function ($query) use ($search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                if (preg_match('/^YT(\d+)$/i', $search, $matches)) {
                    $query->orWhereKey((int) $matches[1]);
                }
            });
        })->latest()->paginate(12)->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function customer(Customer $customer): View
    {
        $customer->load(['bookings' => fn ($query) => $query->with('treatmentPackage')->latest('starts_at'), 'notes' => fn ($query) => $query->latest()]);

        return view('customers.show', compact('customer'));
    }

    public function storeCustomer(Request $request): RedirectResponse
    {
        $customer = Customer::create($this->customerData($request));

        return redirect()->route('customers.show', $customer)->with('success', 'Pelanggan berjaya ditambah.');
    }

    public function updateCustomer(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->customerData($request));

        return back()->with('success', 'Maklumat pelanggan dikemas kini.');
    }

    public function deleteCustomer(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan dipadam.');
    }

    private function customerData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'age' => ['nullable', 'integer', 'between:0,120'],
            'gender' => ['nullable', Rule::in(['Lelaki', 'Perempuan', 'Lain-lain'])],
            'health_history' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
