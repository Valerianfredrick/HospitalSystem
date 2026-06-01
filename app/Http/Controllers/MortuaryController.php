<?php

namespace App\Http\Controllers;

use App\Models\MortuaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MortuaryController extends Controller
{
    public function index()
    {
        $records = MortuaryRecord::with(['patient', 'referredBy', 'receivedBy'])
            ->latest()
            ->paginate(20);

        $stats = [
            'pending'  => MortuaryRecord::pending()->count(),
            'received' => MortuaryRecord::received()->count(),
            'released' => MortuaryRecord::where('status', 'released')->count(),
        ];

        return view('mortuary.index', compact('records', 'stats'));
    }

    public function show(MortuaryRecord $record)
    {
        $record->load(['patient', 'referredBy', 'receivedBy']);
        return view('mortuary.show', compact('record'));
    }

    public function receive(MortuaryRecord $record)
    {
        $record->update([
            'status'      => 'received',
            'received_by' => Auth::id(),
            'received_at' => now(),
        ]);

        return back()->with('success', 'Body received and recorded.');
    }

    public function release(Request $request, MortuaryRecord $record)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $record->update([
            'status'      => 'released',
            'released_at' => now(),
            'notes'       => $request->notes,
        ]);

        return back()->with('success', 'Body released successfully.');
    }
}
