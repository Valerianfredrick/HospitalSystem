<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockItemRequest;
use App\Models\Prescription;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacyController extends Controller
{
    const CATEGORIES = [
        'Antibiotic', 'Analgesic', 'Antihypertensive', 'Antidiabetic',
        'Antihistamine', 'IV Fluid', 'Supplement', 'Other',
    ];

    const UNITS = [
        'Tablet', 'Capsule', 'Syrup (ml)', 'Injection (vial)',
        'Sachet', 'Cream (tube)', 'Drops', 'Inhaler', 'Patch', 'Other',
    ];

    // ── Index: stock + pending prescriptions ─────────────────────────────
    public function index()
    {
        $stockItems = StockItem::active()->orderBy('name')->paginate(20);

        // All undispensed prescriptions with patient + doctor info
        $pendingRx = Prescription::with(['patient', 'doctor'])
            ->where('is_dispensed', false)
            ->latest()
            ->get();

        return view('pharmacy.index', [
            'stockItems'           => $stockItems,
            'totalItems'           => StockItem::active()->count(),
            'totalStock'           => StockItem::active()->sum('quantity'),
            'lowStockCount'        => StockItem::active()->lowStock()->count(),
            'pendingPrescriptions' => $pendingRx->count(),
            'pendingRx'            => $pendingRx,
        ]);
    }

    // ── Prescription queue page ───────────────────────────────────────────
    public function prescriptions()
    {
        $pendingRx = Prescription::with(['patient', 'doctor'])
            ->where('is_dispensed', false)
            ->latest()
            ->paginate(20);

        $dispensedRx = Prescription::with(['patient', 'doctor', 'dispensedBy'])
            ->where('is_dispensed', true)
            ->latest('dispensed_at')
            ->paginate(20);

        return view('pharmacy.prescriptions', compact('pendingRx', 'dispensedRx'));
    }

    // ── Dispense a prescription ───────────────────────────────────────────
    public function dispense(Request $request, Prescription $prescription)
    {
        if ($prescription->is_dispensed) {
            return back()->with('error', 'This prescription has already been dispensed.');
        }

        $prescription->update([
            'is_dispensed'  => true,
            'dispensed_at'  => now(),
            'dispensed_by'  => Auth::id(),
        ]);

        return back()->with('success',
            "Prescription for {$prescription->patient->name} ({$prescription->medication_name}) marked as dispensed."
        );
    }

    // ── Create stock item ─────────────────────────────────────────────────
    public function create()
    {
        return view('pharmacy.create', [
            'categories' => self::CATEGORIES,
            'units'      => self::UNITS,
        ]);
    }

    public function store(StoreStockItemRequest $request)
    {
        $data               = $request->validated();
        $data['created_by'] = Auth::id();

        foreach (['barcode', 'sku'] as $col) {
            if (isset($data[$col]) && $data[$col] === '') $data[$col] = null;
        }

        StockItem::create($data);

        return redirect()->route('pharmacy.index')
            ->with('success', "Medicine \"{$data['name']}\" added to stock successfully.");
    }

    // ── Edit / Update stock item ──────────────────────────────────────────
    public function edit(StockItem $stockItem)
    {
        return view('pharmacy.edit', [
            'item'       => $stockItem,
            'categories' => self::CATEGORIES,
            'units'      => self::UNITS,
        ]);
    }

    public function update(StoreStockItemRequest $request, StockItem $stockItem)
    {
        $data = $request->validated();

        foreach (['barcode', 'sku'] as $col) {
            if (isset($data[$col]) && $data[$col] === '') $data[$col] = null;
        }

        $stockItem->update($data);

        return redirect()->route('pharmacy.index')
            ->with('success', "Medicine \"{$stockItem->name}\" updated successfully.");
    }

    // ── Restock ───────────────────────────────────────────────────────────
    public function showRestock(StockItem $stockItem)
    {
        return view('pharmacy.restock', ['item' => $stockItem]);
    }

    public function restock(Request $request, StockItem $stockItem)
    {
        $request->validate([
            'quantity'    => ['required', 'integer', 'min:1'],
            'expiry_date' => ['nullable', 'date', 'after:today'],
            'unit_price'  => ['nullable', 'numeric', 'min:0'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        $stockItem->increment('quantity', $request->quantity);

        $stockItem->fill(array_filter([
            'expiry_date' => $request->expiry_date,
            'unit_price'  => $request->unit_price,
        ]))->save();

        return redirect()->route('pharmacy.index')
            ->with('success', "Restocked {$request->quantity} units of \"{$stockItem->name}\".");
    }
}
