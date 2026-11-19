<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HealthHistoryController extends Controller
{
    public function healthHistory(Request $request): View
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

        return view('health-history.index', compact('customers', 'search'));
    }

    public function updateHealthHistory(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate(['health_history' => ['nullable', 'string', 'max:10000']]);
        $customer->update($data);

        return back()->with('success', 'Sejarah kesihatan berjaya disimpan.');
    }
}
