<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah MoM — Helas Corp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0ebf8;
            min-height: 100vh;
            padding: 40px 16px;
            color: #202124;
        }

        .form-wrap {
            max-width: 640px;
            margin: 0 auto;
        }

        /* ── Flash notifications ─────────────────────────────── */
        .flash {
            padding: 14px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-success {
            background: #e6f4ea;
            color: #1e7e34;
            border: 1px solid #b7dfc4;
        }

        .flash-error {
            background: #fce8e6;
            color: #c5221f;
            border: 1px solid #f5c6c5;
        }

        /* ── Header card ─────────────────────────────────────── */
        .form-header {
            background: #673ab7;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 12px;
            border-top: 4px solid #4527a0;
        }

        .form-header h1 {
            color: #fff;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-header p {
            color: rgba(255, 255, 255, .85);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ── Field cards ─────────────────────────────────────── */
        .field-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12);
            padding: 24px;
            margin-bottom: 12px;
        }

        .field-label {
            display: block;
            font-size: 15px;
            color: #202124;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .field-label .req {
            color: #d93025;
            margin-left: 4px;
        }

        /* Inputs and textareas: only bottom border */
        .field-input,
        .field-textarea {
            width: 100%;
            border: none;
            border-bottom: 1px solid #dadce0;
            outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #202124;
            padding: 6px 0;
            background: transparent;
            transition: border-bottom .15s;
        }

        .field-input:focus,
        .field-textarea:focus {
            border-bottom: 2px solid #673ab7;
        }

        .field-textarea {
            resize: vertical;
            min-height: 80px;
            line-height: 1.55;
        }

        .field-hint {
            font-size: 12px;
            color: #5f6368;
            margin-top: 6px;
        }

        .field-error {
            font-size: 12px;
            color: #d93025;
            margin-top: 5px;
        }

        /* ── Radio group (Google Form style) ────────────────── */
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: 4px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 4px;
            border-radius: 4px;
            cursor: pointer;
        }

        .radio-option:hover {
            background: #f8f4ff;
        }

        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #673ab7;
            cursor: pointer;
            flex-shrink: 0;
        }

        .radio-option label {
            font-size: 14px;
            color: #202124;
            cursor: pointer;
            user-select: none;
        }

        /* ── Action buttons card ─────────────────────────────── */
        .action-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12);
            padding: 16px 24px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit {
            background: #673ab7;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background .2s;
        }

        .btn-submit:hover {
            background: #5e35b1;
        }

        .btn-cancel {
            background: transparent;
            color: #673ab7;
            border: none;
            border-radius: 4px;
            padding: 10px 16px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s;
            display: inline-block;
        }

        .btn-cancel:hover {
            background: #f3e5f5;
        }
    </style>
</head>

<body>
    <div class="form-wrap">

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="flash flash-success" id="flashSuccess">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error') || $errors->has('error'))
            <div class="flash flash-error" id="flashError">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                {{ session('error') ?? $errors->first('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="form-header">
            <h1>Minutes of Meeting</h1>
            <p>Isi form berikut untuk mencatat hasil rapat tim.</p>
        </div>

        <form method="POST" action="{{ route('admin.mom.formSubmit') }}" novalidate>
            @csrf
            <input type="hidden" name="unit" value="{{ $unit }}">
            <input type="hidden" name="owner_username" value="{{ $username }}">

            {{-- Card 1: Tanggal --}}
            <div class="field-card">
                <label class="field-label" for="tanggal">Tanggal Rapat <span class="req">*</span></label>
                <input type="date" id="tanggal" name="tanggal" class="field-input"
                    value="{{ old('tanggal', date('Y-m-d')) }}" required>
                @if ($errors->has('tanggal'))
                    <div class="field-error">{{ $errors->first('tanggal') }}</div>
                @endif
            </div>

            {{-- Card 2: Keterangan --}}
            <div class="field-card">
                <label class="field-label" for="keterangan">Keterangan / Poin Rapat <span
                        class="req">*</span></label>
                <textarea id="keterangan" name="keterangan" class="field-textarea" rows="5"
                    placeholder="Tuliskan poin-poin hasil rapat..." required>{{ old('keterangan') }}</textarea>
                @if ($errors->has('keterangan'))
                    <div class="field-error">{{ $errors->first('keterangan') }}</div>
                @endif
            </div>

            {{-- Card 3: Deadline --}}
            <div class="field-card">
                <label class="field-label" for="deadline">Deadline</label>
                <input type="date" id="deadline" name="deadline" class="field-input" value="{{ old('deadline') }}">
                <div class="field-hint">Kosongkan jika belum ditentukan</div>
                @if ($errors->has('deadline'))
                    <div class="field-error">{{ $errors->first('deadline') }}</div>
                @endif
            </div>

            {{-- Card 4: PIC --}}
            <div class="field-card">
                <label class="field-label" for="pic">PIC / Penanggung Jawab <span class="req">*</span></label>
                <input type="text" id="pic" name="pic" class="field-input"
                    value="{{ old('pic', isset($owner) ? $owner->name : (Auth::check() ? Auth::user()->name : '')) }}" placeholder="Nama penanggung jawab" required>
                @if ($errors->has('pic'))
                    <div class="field-error">{{ $errors->first('pic') }}</div>
                @endif
            </div>

            {{-- Card 5: Target --}}
            <div class="field-card">
                <label class="field-label" for="target">Target <span class="req">*</span></label>
                <textarea id="target" name="target" class="field-textarea" rows="3" placeholder="Target yang ingin dicapai"
                    required>{{ old('target') }}</textarea>
                @if ($errors->has('target'))
                    <div class="field-error">{{ $errors->first('target') }}</div>
                @endif
            </div>

            {{-- Card 6: Hasil --}}
            <div class="field-card">
                <label class="field-label" for="hasil">Hasil</label>
                <textarea id="hasil" name="hasil" class="field-textarea" rows="3"
                    placeholder="Isi setelah task selesai — boleh dikosongkan">{{ old('hasil') }}</textarea>
                @if ($errors->has('hasil'))
                    <div class="field-error">{{ $errors->first('hasil') }}</div>
                @endif
            </div>

            {{-- Card 7: Status --}}
            <div class="field-card">
                <label class="field-label">Status <span class="req">*</span></label>
                <div class="radio-group">
                    @foreach (['Progress', 'Done', 'Overdue'] as $opt)
                        <div class="radio-option">
                            <input type="radio" id="status_{{ $opt }}" name="status"
                                value="{{ $opt }}"
                                {{ old('status', 'Progress') === $opt ? 'checked' : '' }}>
                            <label for="status_{{ $opt }}">{{ $opt }}</label>
                        </div>
                    @endforeach
                </div>
                @if ($errors->has('status'))
                    <div class="field-error">{{ $errors->first('status') }}</div>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="action-card">
                <button type="submit" class="btn-submit">Kirim Formulir</button>
                @if (Auth::check())
                    <a href="{{ route('admin.mom.index', ['unit' => $unit]) }}" class="btn-cancel">Batal</a>
                @else
                    <a href="javascript:history.back()" class="btn-cancel">Batal</a>
                @endif
            </div>

        </form>
    </div>

    <script>
        // Auto-dismiss flash notifications after 4 seconds
        ['flashSuccess', 'flashError'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                setTimeout(function() {
                    el.style.transition = 'opacity .4s';
                    el.style.opacity = '0';
                    setTimeout(function() {
                        el.remove();
                    }, 400);
                }, 4000);
            }
        });
    </script>
</body>

</html>
