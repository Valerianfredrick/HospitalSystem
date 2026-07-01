<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Patient — {{ config('app.name', 'MediCore HMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                        primary: { 50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',800:'#5b21b6',900:'#4c1d95' },
                        secondary: { 50:'#edfafa',100:'#d5f5f6',200:'#afecef',300:'#7edce2',400:'#16bdca',500:'#0694a2',600:'#047481',700:'#036672',800:'#05505c',900:'#014451' },
                    }, fontFamily: { sans: ['DM Sans', 'sans-serif'] } } } }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        body { background: #f8f7ff; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #a78bfa; box-shadow: 0 0 0 3px rgba(167,139,250,0.2); outline: none; }
        .form-input, .form-select, .form-textarea { transition: border-color 0.15s, box-shadow 0.15s; }
    </style>
</head>
<body class="min-h-screen p-6">

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('receptionist.dashboard') }}" class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-primary-600 transition-colors">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="font-bold text-gray-800 text-lg">Register New Patient</h1>
            <p class="text-xs text-gray-400">The patient will be automatically routed to a matching doctor based on the diagnosis below.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
            <div class="flex items-center gap-2 font-medium mb-1"><i class="fas fa-exclamation-circle"></i> Please fix the following:</div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('receptionist.patients.store') }}" class="bg-white rounded-2xl border border-primary-100 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       class="form-input w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of birth</label>
                <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                       class="form-input w-full px-4 py-2.5 border {{ $errors->has('date_of_birth') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm">
                @error('date_of_birth')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select id="gender" name="gender" required
                        class="form-select w-full px-4 py-2.5 border {{ $errors->has('gender') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm">
                    <option value="">Select…</option>
                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                        <option value="{{ $val }}" {{ old('gender') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('gender')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                       class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
            </div>
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <input id="address" type="text" name="address" value="{{ old('address') }}"
                   class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Emergency contact name</label>
                <input id="emergency_contact_name" type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                       class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Emergency contact phone</label>
                <input id="emergency_contact_phone" type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                       class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
            </div>
        </div>

        <div>
            <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-1">What is the patient suffering from?</label>
            <textarea id="diagnosis" name="diagnosis" rows="3" required
                      placeholder="e.g. severe toothache and swollen gum"
                      class="form-textarea w-full px-4 py-2.5 border {{ $errors->has('diagnosis') ? 'border-red-400' : 'border-gray-300' }} rounded-xl text-sm">{{ old('diagnosis') }}</textarea>
            @error('diagnosis')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-gray-400">
                This is scanned automatically to route the patient to the right doctor
                (e.g. mentioning "tooth" or "teeth" routes to Dentistry).
            </p>
        </div>

        <div>
            <label for="specialty" class="block text-sm font-medium text-gray-700 mb-1">Route to department</label>
            <select id="specialty" name="specialty"
                    class="form-select w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
                <option value="">Auto-detect from diagnosis</option>
                @foreach (\App\Services\TriageService::labels() as $value => $label)
                    <option value="{{ $value }}" {{ old('specialty') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-400">
                Leave on "Auto-detect" unless you need to override the suggested department.
            </p>
        </div>

        @if($wards->isNotEmpty())
            <div>
                <label for="bed_id" class="block text-sm font-medium text-gray-700 mb-1">Assign a bed (optional)</label>
                <select id="bed_id" name="bed_id"
                        class="form-select w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm">
                    <option value="">No bed yet</option>
                    @foreach($wards as $ward)
                        @php $availableBeds = $ward->beds->where('status', 'available'); @endphp
                        @if($availableBeds->isNotEmpty())
                            <optgroup label="{{ $ward->name }}">
                                @foreach($availableBeds as $bed)
                                    <option value="{{ $bed->id }}" {{ old('bed_id') == $bed->id ? 'selected' : '' }}>
                                        Bed {{ $bed->bed_number }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif

        <button type="submit"
                class="w-full py-3 bg-gradient-to-r from-primary-600 to-secondary-600 text-white font-semibold rounded-xl shadow-lg hover:opacity-90 transition-all text-sm flex items-center justify-center gap-2">
            <i class="fas fa-user-plus"></i> Register Patient
        </button>
    </form>
</div>

</body>
</html>
