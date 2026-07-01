{{--
    ADD to resources/views/patients/show.blade.php — place this near the
    top of the page, e.g. right under the patient name/status header,
    or inside whatever "actions" panel that view already has.

    Only rendered when the logged-in user IS the attending doctor
    (otherwise they'd be on show_restricted.blade.php instead, so this
    block can assume Auth::user() is either the attending doctor or a
    nurse/admin). Nurses/admins can still see the form per this check —
    if you want it doctor-only visually, wrap with
    @if(auth()->user()->role === 'doctor')
--}}

@if($patient->doctor_id === auth()->id() || auth()->user()->role !== 'doctor')
    <div class="bg-white rounded-2xl border border-primary-100 p-5 mb-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-bold text-gray-800 text-sm">Attending Doctor</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $patient->doctor->name ?? 'Unassigned — pick a doctor below' }}
                </p>
            </div>
        </div>

        @if($otherDoctors->isNotEmpty())
            <form method="POST" action="{{ route('patients.reassign', $patient) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="doctor_id" required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-xl text-sm bg-white">
                    <option value="">Reassign to…</option>
                    @foreach($otherDoctors as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->name }} @if($doc->specialty)({{ \App\Services\TriageService::labels()[$doc->specialty] ?? $doc->specialty }})@endif</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-colors whitespace-nowrap">
                    <i class="fas fa-exchange-alt mr-1"></i> Reassign
                </button>
            </form>
        @else
            <p class="text-xs text-gray-400">No other doctors available to reassign to.</p>
        @endif
    </div>
@endif
