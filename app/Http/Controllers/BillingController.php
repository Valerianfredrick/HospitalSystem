<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['patient', 'createdBy'])
            ->latest()
            ->paginate(20);

        $stats = [
            'unpaid'       => Bill::where('status', 'unpaid')->count(),
            'paid_today'   => Bill::where('status', 'paid')->whereDate('paid_at', today())->count(),
            'total_today'  => Bill::whereDate('created_at', today())->sum('grand_total'),
            'outstanding'  => Bill::whereIn('status', ['unpaid', 'partial'])->sum('balance'),
        ];

        return view('billing.index', compact('bills', 'stats'));
    }

    public function show(Bill $bill)
    {
        $bill->load(['patient.labRequests.requestedBy', 'patient.prescriptions.stockItem', 'createdBy', 'processedBy']);
        return view('billing.show', compact('bill'));
    }

    public function process(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'amount_paid'    => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,insurance,mobile_money,bank',
            'notes'          => 'nullable|string',
            'extra_charges'  => 'nullable|array',
            'extra_charges.*.label'  => 'required_with:extra_charges|string',
            'extra_charges.*.amount' => 'required_with:extra_charges|numeric|min:0',
        ]);

        $extraTotal = collect($validated['extra_charges'] ?? [])->sum('amount');
        $grandTotal = $bill->bed_total + $bill->lab_total + $bill->drugs_total + $extraTotal;
        $amountPaid = $validated['amount_paid'];
        $balance    = max(0, $grandTotal - $amountPaid);

        $status = $balance == 0 ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');

        $bill->update([
            'extra_charges'  => $validated['extra_charges'] ?? [],
            'grand_total'    => $grandTotal,
            'amount_paid'    => $amountPaid,
            'balance'        => $balance,
            'status'         => $status,
            'payment_method' => $validated['payment_method'],
            'notes'          => $validated['notes'] ?? null,
            'processed_by'   => Auth::id(),
            'paid_at'        => $status === 'paid' ? now() : null,
        ]);

        return redirect()
            ->route('billing.show', $bill)
            ->with('success', 'Payment recorded successfully.');
    }

    public function waive(Request $request, Bill $bill)
    {
        $bill->update([
            'status'       => 'waived',
            'balance'      => 0,
            'processed_by' => Auth::id(),
            'notes'        => $request->reason ?? 'Waived by accountant',
            'paid_at'      => now(),
        ]);

        return back()->with('success', 'Bill has been waived.');
    }
}
