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
            'unpaid'      => Bill::where('status', 'unpaid')->count(),
            'paid_today'  => Bill::where('status', 'paid')->whereDate('paid_at', today())->count(),
            'total_today' => Bill::whereDate('created_at', today())->sum('grand_total'),
            'outstanding' => Bill::whereIn('status', ['unpaid', 'partial'])->sum('balance'),
        ];

        return view('billing.index', compact('bills', 'stats'));
    }

    public function show(Bill $bill)
    {
        $bill->load([
            'patient.labRequests.requestedBy',
            'patient.prescriptions.stockItem',
            'createdBy',
            'processedBy',
        ]);

        return view('billing.show', compact('bill'));
    }

    public function process(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'amount_paid'        => ['required', 'numeric', 'min:0'],
            'payment_method'     => ['required', 'in:cash,mobile_money,insurance,bank'],

            // Mobile money
            'mobile_provider'    => ['required_if:payment_method,mobile_money', 'nullable', 'string'],
            'mobile_phone'       => [
                'required_if:payment_method,mobile_money',
                'nullable',
                'string',
                'max:9',
                'regex:/^[67]\d{8}$/',  // TZ mobile: 6xx or 7xx, 9 digits
            ],

            // Insurance
            'insurance_provider' => ['required_if:payment_method,insurance', 'nullable', 'string'],
            'insurance_ref'      => ['nullable', 'string', 'max:100'],

            // Bank
            'bank_name'          => ['required_if:payment_method,bank', 'nullable', 'string'],
            'bank_ref'           => ['nullable', 'string', 'max:100'],

            // Extra charges
            'notes'                      => ['nullable', 'string'],
            'extra_charges'              => ['nullable', 'array'],
            'extra_charges.*.label'      => ['required_with:extra_charges', 'string'],
            'extra_charges.*.amount'     => ['required_with:extra_charges', 'numeric', 'min:0'],
        ]);

        // Build payment details snapshot (stored as JSON)
        $paymentDetails = match($validated['payment_method']) {
            'mobile_money' => [
                'provider' => $validated['mobile_provider'] ?? null,
                'phone'    => '+255' . $validated['mobile_phone'],
            ],
            'insurance' => [
                'provider' => $validated['insurance_provider'] ?? null,
                'ref'      => $validated['insurance_ref'] ?? null,
            ],
            'bank' => [
                'bank' => $validated['bank_name'] ?? null,
                'ref'  => $validated['bank_ref'] ?? null,
            ],
            default => [], // cash — no extra details needed
        };

        // Recalculate totals (include any new extra charges)
        $extraTotal = collect($validated['extra_charges'] ?? [])->sum('amount');
        $grandTotal = $bill->bed_total
            + $bill->lab_total
            + $bill->drugs_total
            + $bill->consultation_fee   // ← added
            + $extraTotal;
        $amountPaid = (float) $validated['amount_paid'];
        $balance    = max(0, $grandTotal - $amountPaid);
        $status     = $balance == 0 ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');

        $bill->update([
            'extra_charges'   => $validated['extra_charges'] ?? [],
            'grand_total'     => $grandTotal,
            'amount_paid'     => $amountPaid,
            'balance'         => $balance,
            'status'          => $status,
            'payment_method'  => $validated['payment_method'],
            'payment_details' => $paymentDetails,   // JSON column — see note below
            'notes'           => $validated['notes'] ?? null,
            'processed_by'    => Auth::id(),
            'paid_at'         => $status === 'paid' ? now() : null,
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
