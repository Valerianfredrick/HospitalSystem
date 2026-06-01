@extends('layouts.app')

@section('title', 'Add Medicine')
@section('page-title', 'Add Medicine')
@section('page-subtitle', 'Add a new medicine or supply item to the pharmacy stock')

@section('content')

    {{-- Back link --}}
    <div style="margin-bottom:20px;">
        <a href="{{ route('pharmacy.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-muted);font-size:13px;text-decoration:none;">
            <i class="fas fa-arrow-left"></i> Back to Pharmacy
        </a>
    </div>

    {{-- Validation errors banner --}}
    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;">
            <i class="fas fa-exclamation-circle" style="color:#ef4444;margin-top:2px;flex-shrink:0;"></i>
            <div>
                <div style="font-weight:600;font-size:13px;color:#b91c1c;margin-bottom:4px;">Please fix the following errors:</div>
                <ul style="margin:0;padding-left:16px;font-size:12px;color:#b91c1c;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('pharmacy.store') }}" id="addMedicineForm">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

            {{-- ── LEFT COLUMN ─────────────────────────────────────────────── --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Section: Basic Information --}}
                <div class="card" style="padding:24px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--grad);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-pills" style="color:#fff;font-size:13px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;">Basic Information</div>
                            <div style="font-size:11px;color:var(--text-muted);">Core identity of the medicine</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        {{-- Medicine Name --}}
                        <div style="grid-column:1/-1;">
                            <label class="form-label">Medicine Name <span style="color:#ef4444;">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Amoxicillin 500mg"
                                   autofocus>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Generic Name --}}
                        <div style="grid-column:1/-1;">
                            <label class="form-label">Generic / INN Name</label>
                            <input type="text"
                                   name="generic_name"
                                   class="form-control @error('generic_name') is-invalid @enderror"
                                   value="{{ old('generic_name') }}"
                                   placeholder="e.g. Amoxicillin">
                            @error('generic_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="form-label">Category <span style="color:#ef4444;">*</span></label>
                            <select name="category" class="form-control @error('category') is-invalid @enderror">
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Select category…</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Unit --}}
                        <div>
                            <label class="form-label">Dispensing Unit <span style="color:#ef4444;">*</span></label>
                            <select name="unit" class="form-control @error('unit') is-invalid @enderror">
                                <option value="" disabled {{ old('unit') ? '' : 'selected' }}>Select unit…</option>
                                @foreach($units as $u)
                                    <option value="{{ $u }}" {{ old('unit') === $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                            @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Section: Identification --}}
                <div class="card" style="padding:24px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);">
                        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#0ea5e9,#6366f1);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-barcode" style="color:#fff;font-size:13px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;">Barcode & SKU</div>
                            <div style="font-size:11px;color:var(--text-muted);">Optional — for scanning & inventory tracking</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        {{-- Barcode --}}
                        <div>
                            <label class="form-label">Barcode</label>
                            <div style="position:relative;">
                                <i class="fas fa-barcode" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px;pointer-events:none;"></i>
                                <input type="text"
                                       name="barcode"
                                       id="barcodeInput"
                                       class="form-control @error('barcode') is-invalid @enderror"
                                       value="{{ old('barcode') }}"
                                       placeholder="Scan or type barcode"
                                       style="padding-left:36px;">
                            </div>
                            @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SKU --}}
                        <div>
                            <label class="form-label">SKU / Internal Code</label>
                            <div style="position:relative;">
                                <i class="fas fa-hashtag" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;pointer-events:none;"></i>
                                <input type="text"
                                       name="sku"
                                       class="form-control @error('sku') is-invalid @enderror"
                                       value="{{ old('sku') }}"
                                       placeholder="e.g. MED-0042"
                                       style="padding-left:36px;">
                            </div>
                            @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Section: Stock Levels --}}
                <div class="card" style="padding:24px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);">
                        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-boxes" style="color:#fff;font-size:13px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;">Stock Levels</div>
                            <div style="font-size:11px;color:var(--text-muted);">Opening quantity and reorder threshold</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">

                        {{-- Opening Quantity --}}
                        <div>
                            <label class="form-label">Opening Quantity <span style="color:#ef4444;">*</span></label>
                            <input type="number"
                                   name="quantity"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 0) }}"
                                   min="0"
                                   placeholder="0">
                            @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Reorder Level --}}
                        <div>
                            <label class="form-label">Reorder Level <span style="color:#ef4444;">*</span></label>
                            <input type="number"
                                   name="reorder_level"
                                   class="form-control @error('reorder_level') is-invalid @enderror"
                                   value="{{ old('reorder_level', 10) }}"
                                   min="0"
                                   placeholder="10">
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Alert when stock falls below this</div>
                            @error('reorder_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Unit Price --}}
                        <div>
                            <label class="form-label">Unit Price (TZS)</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:11px;font-weight:600;pointer-events:none;">TZS</span>
                                <input type="number"
                                       name="unit_price"
                                       class="form-control @error('unit_price') is-invalid @enderror"
                                       value="{{ old('unit_price') }}"
                                       min="0"
                                       step="0.01"
                                       placeholder="0.00"
                                       style="padding-left:44px;">
                            </div>
                            @error('unit_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Section: Dates --}}
                <div class="card" style="padding:24px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);">
                        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-calendar-alt" style="color:#fff;font-size:13px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;">Dates</div>
                            <div style="font-size:11px;color:var(--text-muted);">Manufacturing and expiry information</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        <div>
                            <label class="form-label">Manufacture Date</label>
                            <input type="date"
                                   name="manufacture_date"
                                   class="form-control @error('manufacture_date') is-invalid @enderror"
                                   value="{{ old('manufacture_date') }}"
                                   max="{{ date('Y-m-d') }}">
                            @error('manufacture_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Expiry Date</label>
                            <input type="date"
                                   name="expiry_date"
                                   class="form-control @error('expiry_date') is-invalid @enderror"
                                   value="{{ old('expiry_date') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Section: Notes --}}
                <div class="card" style="padding:24px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border);">
                        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-sticky-note" style="color:#fff;font-size:13px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;">Additional Notes</div>
                            <div style="font-size:11px;color:var(--text-muted);">Storage instructions, warnings, remarks</div>
                        </div>
                    </div>

                    <textarea name="notes"
                              class="form-control @error('notes') is-invalid @enderror"
                              rows="3"
                              placeholder="e.g. Store below 25°C. Keep away from light. Shake before use."
                              style="resize:vertical;">{{ old('notes') }}</textarea>
                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>{{-- /LEFT COLUMN --}}

            {{-- ── RIGHT COLUMN ────────────────────────────────────────────── --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Live Preview card --}}
                <div class="card" style="padding:20px;">
                    <div style="font-weight:700;font-size:13px;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-eye" style="color:var(--violet-lt);"></i> Live Preview
                    </div>
                    <div style="border:1px solid var(--border);border-radius:12px;padding:16px;background:var(--surface);">
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                            <div style="width:40px;height:40px;border-radius:10px;background:var(--grad-soft);display:flex;align-items:center;justify-content:center;border:1px solid var(--border);flex-shrink:0;">
                                <i class="fas fa-capsules" style="color:var(--violet-lt);font-size:15px;"></i>
                            </div>
                            <div>
                                <div id="prev-name" style="font-weight:700;font-size:14px;color:var(--text-primary);">Medicine Name</div>
                                <div id="prev-generic" style="font-size:11px;color:var(--text-muted);">Generic name</div>
                            </div>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;">
                            <span id="prev-cat" class="pill violet" style="font-size:11px;">Category</span>
                            <span id="prev-unit" class="pill" style="font-size:11px;background:var(--surface-2,#f3f4f6);color:var(--text-muted);">Unit</span>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px;">
                            <div>
                                <div style="color:var(--text-muted);margin-bottom:2px;">Quantity</div>
                                <div id="prev-qty" style="font-weight:700;font-size:16px;font-family:'Syne',sans-serif;">0</div>
                            </div>
                            <div>
                                <div style="color:var(--text-muted);margin-bottom:2px;">Unit Price</div>
                                <div id="prev-price" style="font-weight:600;">—</div>
                            </div>
                            <div>
                                <div style="color:var(--text-muted);margin-bottom:2px;">Expiry</div>
                                <div id="prev-expiry" style="font-weight:500;">—</div>
                            </div>
                            <div>
                                <div style="color:var(--text-muted);margin-bottom:2px;">Status</div>
                                <span id="prev-status" class="pill green" style="font-size:11px;">In Stock</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Supplier card --}}
                <div class="card" style="padding:20px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border);">
                        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#06b6d4,#0891b2);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-truck" style="color:#fff;font-size:12px;"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13px;">Supplier</div>
                            <div style="font-size:11px;color:var(--text-muted);">Optional supplier details</div>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <div>
                            <label class="form-label">Supplier Name</label>
                            <input type="text"
                                   name="supplier_name"
                                   class="form-control @error('supplier_name') is-invalid @enderror"
                                   value="{{ old('supplier_name') }}"
                                   placeholder="e.g. MedSupply Ltd">
                            @error('supplier_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Supplier Contact</label>
                            <input type="text"
                                   name="supplier_contact"
                                   class="form-control @error('supplier_contact') is-invalid @enderror"
                                   value="{{ old('supplier_contact') }}"
                                   placeholder="Phone or email">
                            @error('supplier_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Status card --}}
                <div class="card" style="padding:20px;">
                    <div style="font-weight:700;font-size:13px;margin-bottom:14px;">Item Status</div>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 12px;border:1px solid var(--border);border-radius:10px;background:var(--surface);">
                        <input type="hidden"  name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="isActive"
                               {{ old('is_active', 1) ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--teal,#00897B);">
                        <div>
                            <div style="font-weight:600;font-size:13px;">Active</div>
                            <div style="font-size:11px;color:var(--text-muted);">Visible and dispensable in pharmacy</div>
                        </div>
                    </label>
                </div>

                {{-- Submit --}}
                <div class="card" style="padding:20px;">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:14px;font-weight:700;" id="submitBtn">
                        <i class="fas fa-plus-circle"></i> Add Medicine to Stock
                    </button>
                    <a href="{{ route('pharmacy.index') }}" class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:8px;">
                        Cancel
                    </a>
                </div>

            </div>{{-- /RIGHT COLUMN --}}

        </div>
    </form>

@endsection

@push('styles')
    <style>
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        .form-control {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            background: var(--surface);
            color: var(--text-primary);
            transition: border-color .15s, box-shadow .15s;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--violet-lt, #8b5cf6);
            box-shadow: 0 0 0 3px rgba(109,40,217,.1);
        }
        .form-control.is-invalid {
            border-color: #ef4444;
        }
        .invalid-feedback {
            font-size: 11px;
            color: #ef4444;
            margin-top: 4px;
        }
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 32px;
        }
        @media (max-width: 900px) {
            form > div[style*="grid-template-columns:1fr 340px"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // ── Live preview ────────────────────────────────────────────────────────────
        const previewFields = {
            name:          { input: 'input[name=name]',         el: document.getElementById('prev-name'),    fallback: 'Medicine Name' },
            generic_name:  { input: 'input[name=generic_name]', el: document.getElementById('prev-generic'), fallback: 'Generic name' },
            category:      { input: 'select[name=category]',    el: document.getElementById('prev-cat'),     fallback: 'Category' },
            unit:          { input: 'select[name=unit]',         el: document.getElementById('prev-unit'),    fallback: 'Unit' },
            quantity:      { input: 'input[name=quantity]',     el: document.getElementById('prev-qty'),     fallback: '0' },
            unit_price:    { input: 'input[name=unit_price]',   el: document.getElementById('prev-price'),   fallback: '—' },
            expiry_date:   { input: 'input[name=expiry_date]',  el: document.getElementById('prev-expiry'),  fallback: '—' },
        };

        function updatePreview() {
            const name  = document.querySelector('input[name=name]').value.trim();
            previewFields.name.el.textContent = name || 'Medicine Name';

            const gen   = document.querySelector('input[name=generic_name]').value.trim();
            previewFields.generic_name.el.textContent = gen || 'Generic name';

            const cat   = document.querySelector('select[name=category]').value;
            previewFields.category.el.textContent = cat || 'Category';

            const unit  = document.querySelector('select[name=unit]').value;
            previewFields.unit.el.textContent = unit || 'Unit';

            const qty   = parseInt(document.querySelector('input[name=quantity]').value) || 0;
            previewFields.quantity.el.textContent = qty;

            const price = parseFloat(document.querySelector('input[name=unit_price]').value);
            previewFields.unit_price.el.textContent = isNaN(price) ? '—' : 'TZS ' + price.toLocaleString();

            const expiry = document.querySelector('input[name=expiry_date]').value;
            if (expiry) {
                const d = new Date(expiry);
                previewFields.expiry_date.el.textContent = d.toLocaleDateString('en-GB', { month: 'short', year: 'numeric' });
            } else {
                previewFields.expiry_date.el.textContent = '—';
            }

            // Stock status
            const reorder = parseInt(document.querySelector('input[name=reorder_level]').value) || 10;
            const statusEl = document.getElementById('prev-status');
            statusEl.className = 'pill';
            if (qty === 0) {
                statusEl.classList.add('red');
                statusEl.textContent = 'Out of Stock';
            } else if (qty <= reorder) {
                statusEl.classList.add('amber');
                statusEl.textContent = 'Low Stock';
            } else {
                statusEl.classList.add('green');
                statusEl.textContent = 'In Stock';
            }
        }

        document.getElementById('addMedicineForm').querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('input',  updatePreview);
            el.addEventListener('change', updatePreview);
        });
        updatePreview();

        // ── Barcode scanner support (keyboard wedge scanner fires Enter) ────────────
        document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // don't submit form mid-scan
            }
        });

        // ── Submit guard ─────────────────────────────────────────────────────────────
        document.getElementById('addMedicineForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';
        });
    </script>
@endpush
