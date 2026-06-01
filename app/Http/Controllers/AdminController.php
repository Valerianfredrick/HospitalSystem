<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\StockItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_patients'  => Patient::count(),
            'new_this_month'  => Patient::whereMonth('admitted_at', now()->month)->count(),
            'inpatients'      => Patient::admitted()->count(),
            'bed_occupancy'   => $this->bedOccupancyPercent(),
            'doctors'         => User::where('role', 'doctor')->count(),
            'staff'           => User::count(),
            'low_stock_items' => StockItem::lowStock()->count(),
        ];

        $daily_flow = Patient::selectRaw('DATE(admitted_at) as date, COUNT(*) as admitted')
            ->whereDate('admitted_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                $row->discharged = Patient::whereDate('discharged_at', $row->date)->count();
                return $row;
            });

        $max_daily = max(
            $daily_flow->max('admitted') ?? 1,
            $daily_flow->max('discharged') ?? 1
        );

        $ward_stats = collect([
            (object)['name' => 'General',   'capacity' => 40, 'occupied' => Patient::admitted()->where('ward', 'General')->count()],
            (object)['name' => 'ICU',       'capacity' => 10, 'occupied' => Patient::admitted()->where('ward', 'ICU')->count()],
            (object)['name' => 'Pediatric', 'capacity' => 20, 'occupied' => Patient::admitted()->where('ward', 'Pediatric')->count()],
            (object)['name' => 'Maternity', 'capacity' => 15, 'occupied' => Patient::admitted()->where('ward', 'Maternity')->count()],
            (object)['name' => 'Surgical',  'capacity' => 20, 'occupied' => Patient::admitted()->where('ward', 'Surgical')->count()],
        ]);

        $critical_stock = StockItem::where(function ($q) {
            $q->lowStock()->orWhere(function ($q2) {
                $q2->expiringSoon(30);
            });
        })->orderBy('quantity')->take(8)->get();

        $recent_activity = $this->getRecentActivity();

        return view('admin.dashboard', compact(
            'stats', 'daily_flow', 'max_daily',
            'ward_stats', 'critical_stock', 'recent_activity'
        ));
    }

    public function users()
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(20);
        return view('admin.users', compact('users'));
    }

    private function bedOccupancyPercent(): int
    {
        $total_beds = 105;
        $occupied   = Patient::admitted()->count();
        return $total_beds > 0 ? round(($occupied / $total_beds) * 100) : 0;
    }

    private function getRecentActivity(): array
    {
        $activity = [];

        $recent_admissions = Patient::whereDate('admitted_at', today())
            ->latest('admitted_at')->take(3)->get();

        foreach ($recent_admissions as $p) {
            $activity[] = [
                'message' => "{$p->name} admitted to {$p->ward} ward",
                'time'    => Carbon::parse($p->admitted_at)->diffForHumans(),
                'icon'    => 'fa-user-plus',
                'color'   => 'bg-blue-500',
            ];
        }

        $recent_discharges = Patient::whereDate('discharged_at', today())
            ->latest('discharged_at')->take(2)->get();

        foreach ($recent_discharges as $p) {
            $activity[] = [
                'message' => "{$p->name} discharged ({$p->discharge_condition})",
                'time'    => Carbon::parse($p->discharged_at)->diffForHumans(),
                'icon'    => 'fa-sign-out-alt',
                'color'   => 'bg-green-500',
            ];
        }

        $low_stock = StockItem::lowStock()->take(2)->get();
        foreach ($low_stock as $s) {
            $activity[] = [
                'message' => "Low stock alert: {$s->name} ({$s->quantity} {$s->unit})",
                'time'    => 'Stock alert',
                'icon'    => 'fa-exclamation-triangle',
                'color'   => 'bg-red-500',
            ];
        }

        return array_slice($activity, 0, 8);
    }
}
