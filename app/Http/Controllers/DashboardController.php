<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(Request $request): View
    {
        $month = $this->month($request);
        $start = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $monthBookings = Booking::query()->whereBetween('starts_at', [$start, $end]);
        $count = (clone $monthBookings)->count();
        $confirmed = (clone $monthBookings)->where('status', 'confirmed')->count();
        $completed = (clone $monthBookings)->where('status', 'completed')->count();
        $revenue = Payment::where('status', 'paid')->whereBetween('paid_at', [$start->toDateString(), $end->toDateString()])->sum('amount');
        $upcoming = (clone $monthBookings)->with(['customer', 'treatmentPackage'])->orderBy('starts_at')->limit(8)->get();
        $daily = (clone $monthBookings)->selectRaw('date(starts_at) as booking_day, count(*) as total')
            ->groupBy('booking_day')->pluck('total', 'booking_day');

        return view('dashboard', compact('month', 'start', 'count', 'confirmed', 'completed', 'revenue', 'upcoming', 'daily'));
    }

    private function month(Request $request): string
    {
        $month = (string) $request->query('month', '2027-01');

        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month) ? $month : '2027-01';
    }
}
