<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $query = StockItem::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // ✅ camelCase to match blade
        $stockItems = $query->orderBy('name')->paginate(20)->withQueryString();

        // ✅ flat variables to match blade {{ $totalItems }}, {{ $totalStock }}, etc.
        $totalItems   = StockItem::count();
        $totalStock   = StockItem::sum('quantity');
        $lowStockCount = StockItem::lowStock()->count();

        // ✅ $pendingRx for the prescriptions tab table
        $pendingRx = Prescription::where('is_dispensed', false)
            ->with(['patient', 'prescribedBy'])
            ->latest()
            ->take(10)
            ->get();

        // ✅ $pendingPrescriptions (count) for the stat card
        $pendingPrescriptions = $pendingRx->count();

        return view('pharmacy.index', compact(
            'stockItems',
            'totalItems',
            'totalStock',
            'lowStockCount',
            'pendingPrescriptions',
            'pendingRx',
        ));
    }

    public function create()
    {
        return view('pharmacy.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'required|in:medicine,consumable,equipment',
            'manufacturer'  => 'nullable|string',
            'quantity'      => 'required|integer|min:0',
            'unit'          => 'required|string',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date'   => 'nullable|date|after:today',
            'unit_price'    => 'nullable|numeric|min:0',
            'location'      => 'nullable|string',
        ]);

        $item = StockItem::create($validated);

        StockMovement::create([
            'stock_item_id' => $item->id,
            'user_id'       => Auth::id(),
            'type'          => 'in',
            'quantity'      => $validated['quantity'],
            'notes'         => 'Initial stock',
        ]);

        return redirect()->route('pharmacy.index')
            ->with('success', "{$item->name} added to inventory.");
    }

    public function edit(StockItem $stockItem)
    {
        return view('pharmacy.edit', compact('stockItem'));
    }

    public function update(Request $request, StockItem $stockItem)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'required|in:medicine,consumable,equipment',
            'manufacturer'  => 'nullable|string',
            'reorder_level' => 'nullable|integer|min:0',
            'expiry_date'   => 'nullable|date',
            'unit_price'    => 'nullable|numeric|min:0',
            'location'      => 'nullable|string',
        ]);

        $stockItem->update($validated);

        return redirect()->route('pharmacy.index')
            ->with('success', 'Item updated.');
    }

    public function showRestock(StockItem $stockItem)
    {
        return view('pharmacy.restock', compact('stockItem'));
    }

    public function restock(Request $request, StockItem $stockItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $stockItem) {
            $stockItem->increment('quantity', $request->quantity);

            StockMovement::create([
                'stock_item_id' => $stockItem->id,
                'user_id'       => Auth::id(),
                'type'          => 'in',
                'quantity'      => $request->quantity,
                'notes'         => $request->notes ?? 'Restocked',
            ]);
        });

        return back()->with('success', "{$stockItem->name} restocked by {$request->quantity} {$stockItem->unit}.");
    }

    public function dispense(Prescription $prescription)
    {
        $prescription->update([
            'is_dispensed' => true,
            'dispensed_at' => now(),
            'dispensed_by' => Auth::id(),
        ]);

        return back()->with('success', "Prescription for {$prescription->patient?->name} dispensed.");
    }
}
