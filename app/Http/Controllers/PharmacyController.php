<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockItemRequest;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacyController extends Controller
{
    // ── Constants ─────────────────────────────────────────────────────────

    const CATEGORIES = [
        'Antibiotic', 'Analgesic', 'Antihypertensive', 'Antidiabetic',
        'Antihistamine', 'IV Fluid', 'Supplement', 'Other',
    ];

    const UNITS = [
        'Tablet', 'Capsule', 'Syrup (ml)', 'Injection (vial)',
        'Sachet', 'Cream (tube)', 'Drops', 'Inhaler', 'Patch', 'Other',
    ];

    // ── Index ─────────────────────────────────────────────────────────────

    public function index()
    {
        $stockItems = StockItem::active()
            ->orderBy('name')
            ->paginate(20);

        return view('pharmacy.index', [
            'stockItems'           => $stockItems,
            'totalItems'          => StockItem::active()->count(),
            'totalStock'          => StockItem::active()->sum('quantity'),
            'lowStockCount'       => StockItem::active()->lowStock()->count(),
            'pendingPrescriptions' => 0, // wire up your Prescription model here
            'pendingRx'           => [],
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────────

    public function create()
    {
        return view('pharmacy.create', [
            'categories' => self::CATEGORIES,
            'units'      => self::UNITS,
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────────

    public function store(StoreStockItemRequest $request)
    {
        $data               = $request->validated();
        $data['created_by'] = Auth::id();

        // Normalise empty strings to null for unique-nullable columns
        foreach (['barcode', 'sku'] as $col) {
            if (isset($data[$col]) && $data[$col] === '') {
                $data[$col] = null;
            }
        }

        StockItem::create($data);

        return redirect()
            ->route('pharmacy.index')
            ->with('success', "Medicine \"{$data['name']}\" added to stock successfully.");
    }

    // ── Edit ──────────────────────────────────────────────────────────────

    public function edit(StockItem $stockItem)
    {
        return view('pharmacy.edit', [
            'item'       => $stockItem,
            'categories' => self::CATEGORIES,
            'units'      => self::UNITS,
        ]);
    }

    // ── Update ────────────────────────────────────────────────────────────

    public function update(StoreStockItemRequest $request, StockItem $stockItem)
    {
        // Swap unique rule to ignore current record
        $data = $request->validated();

        foreach (['barcode', 'sku'] as $col) {
            if (isset($data[$col]) && $data[$col] === '') {
                $data[$col] = null;
            }
        }

        $stockItem->update($data);

        return redirect()
            ->route('pharmacy.index')
            ->with('success', "Medicine \"{$stockItem->name}\" updated successfully.");
    }

    // ── Restock ───────────────────────────────────────────────────────────

    public function restock(StockItem $stockItem)
    {
        return view('pharmacy.restock', [
            'item' => $stockItem,
        ]);
    }

    public function addStock(Request $request, StockItem $stockItem)
    {
        $request->validate([
            'quantity'     => ['required', 'integer', 'min:1'],
            'expiry_date'  => ['nullable', 'date', 'after:today'],
            'unit_price'   => ['nullable', 'numeric', 'min:0'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $stockItem->increment('quantity', $request->quantity);

        // Optionally update expiry / price on restock
        $stockItem->fill(array_filter([
            'expiry_date' => $request->expiry_date,
            'unit_price'  => $request->unit_price,
        ]))->save();

        return redirect()
            ->route('pharmacy.index')
            ->with('success', "Restocked {$request->quantity} units of \"{$stockItem->name}\".");
    }

    // ── Dispense prescription ─────────────────────────────────────────────

    public function dispense(Request $request, $prescriptionId)
    {
        // Wire up your Prescription model here.
        // Placeholder logic shown:
        return redirect()
            ->route('pharmacy.index')
            ->with('success', 'Prescription dispensed successfully.');
    }
}
