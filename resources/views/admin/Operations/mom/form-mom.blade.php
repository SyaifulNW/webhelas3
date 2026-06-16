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
            padding: 40px 16px 120px 16px;
            color: #202124;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-wrap {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
        }

        /* ── Flash notifications ─────────────────────────────── */
        .flash {
            padding: 16px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.3s ease;
        }

        .flash-success {
            background: #e6f4ea;
            color: #137333;
            border-left: 5px solid #1e8e3e;
        }

        .flash-error {
            background: #fce8e6;
            color: #c5221f;
            border-left: 5px solid #d93025;
        }

        .error-list ul {
            margin-top: 6px;
            padding-left: 20px;
            font-size: 13px;
        }

        /* ── Header card ─────────────────────────────────────── */
        .form-header-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px 24px;
            margin-bottom: 16px;
            border-top: 10px solid #673ab7;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12), 0 1px 2px rgba(0, 0, 0, .24);
        }

        .form-header-card h1 {
            color: #202124;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .form-header-card p {
            color: #5f6368;
            font-size: 14px;
            line-height: 1.6;
        }

        /* ── Field cards ─────────────────────────────────────── */
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12), 0 1px 2px rgba(0, 0, 0, .24);
            padding: 24px;
            margin-bottom: 16px;
            transition: box-shadow 0.25s ease;
        }

        .card:focus-within {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15), 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #673ab7;
            margin-bottom: 20px;
            border-bottom: 1px solid #f0ebf8;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .field-group {
            margin-bottom: 20px;
        }

        .field-group:last-child {
            margin-bottom: 0;
        }

        .field-label {
            display: block;
            font-size: 15px;
            color: #202124;
            margin-bottom: 8px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .field-group:focus-within .field-label {
            color: #673ab7;
        }

        .field-label .req {
            color: #d93025;
            margin-left: 4px;
        }

        /* Inputs and textareas */
        .field-input,
        .field-textarea {
            width: 100%;
            border: none;
            border-bottom: 1px solid #dadce0;
            outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            color: #202124;
            padding: 8px 0;
            background: transparent;
            transition: border-bottom 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .field-input:focus,
        .field-textarea:focus {
            border-bottom: 2px solid #673ab7;
        }

        .field-textarea {
            resize: vertical;
            min-height: 70px;
            line-height: 1.5;
        }

        .field-hint {
            font-size: 12px;
            color: #70757a;
            margin-top: 6px;
        }

        /* ── Dynamic Point Cards ───────────────────────────── */
        .point-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12), 0 1px 2px rgba(0, 0, 0, .24);
            padding: 24px;
            margin-bottom: 16px;
            border-left: 4px solid #673ab7;
            position: relative;
            transform-origin: top center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .point-card.animate-slide-in {
            animation: slideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .point-card.animate-fade-out {
            animation: fadeOut 0.3s cubic-bezier(0.4, 0, 1, 1) forwards;
        }

        .point-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            border-bottom: 1px dashed #f0ebf8;
            padding-bottom: 8px;
        }

        .point-number {
            font-size: 15px;
            font-weight: 600;
            color: #5f6368;
        }

        .btn-remove-card {
            background: transparent;
            border: none;
            color: #5f6368;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s;
        }

        .btn-remove-card:hover {
            background: #fce8e6;
            color: #c5221f;
        }

        /* ── Action Toolbar ────────────────────────────── */
        .footer-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .12), 0 1px 2px rgba(0, 0, 0, .24);
            padding: 16px 24px;
            margin-top: 24px;
            gap: 12px;
        }

        .actions-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            height: 40px;
            padding: 0 20px;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background: #673ab7;
            color: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-primary:hover {
            background: #5e35b1;
            box-shadow: 0 4px 6px rgba(103, 58, 183, 0.2);
        }

        .btn-outline {
            background: #fff;
            color: #673ab7;
            border: 1px solid #dadce0;
        }

        .btn-outline:hover {
            background: #f8f4ff;
            border-color: #673ab7;
        }

        .btn-cancel {
            color: #5f6368;
            background: transparent;
        }

        .btn-cancel:hover {
            background: #f1f3f4;
            color: #202124;
        }

        /* Centered Add Circle Button */
        .btn-add-circle {
            width: 48px;
            height: 48px;
            background: #673ab7;
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(103, 58, 183, 0.25);
            transition: all 0.2s ease;
        }

        .btn-add-circle:hover {
            transform: scale(1.08);
            background: #5e35b1;
            box-shadow: 0 6px 14px rgba(103, 58, 183, 0.4);
        }

        /* ── Animations ────────────────────────────────────── */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(0.9);
                height: 0;
                margin-bottom: 0;
                padding-top: 0;
                padding-bottom: 0;
                border: none;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 16px 8px;
            }

            .form-header-card h1 {
                font-size: 24px;
            }

            .footer-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .actions-right {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="form-wrap">
        {{-- Flash Success & Confirmation Card --}}
        @if (session('success'))
            <div class="form-header-card" style="padding: 30px 24px; text-align: left;">
                <h1 style="font-size: 32px; font-weight: 700; color: #202124; margin-bottom: 8px;">To do List</h1>
                <p style="color: #5f6368; font-size: 14px; margin-bottom: 24px;">[Notulensi Rapat]</p>
                <div style="font-size: 15px; color: #202124; margin-bottom: 24px; line-height: 1.5;">
                    {{ session('success') }}
                </div>
                <div style="border-top: 1px solid #dadce0; padding-top: 16px; display: flex; flex-direction: column; gap: 12px; align-items: flex-start;">
                    <a href="{{ route('admin.mom.create', ['username' => $username]) }}" style="color: #673ab7; font-size: 14px; text-decoration: none; font-weight: 500; cursor: pointer;">
                        Kirim jawaban lain
                    </a>
                    @if (Auth::check())
                        <a href="{{ route('admin.mom.index', ['unit' => $unit]) }}" style="color: #673ab7; font-size: 14px; text-decoration: none; font-weight: 500; cursor: pointer;">
                            Kembali ke Dashboard MoM
                        </a>
                    @endif
                </div>
            </div>
        @else
            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="flash flash-error" id="flashError">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" style="flex-shrink: 0; margin-top: 2px;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <div class="error-list">
                        <strong>Terjadi kesalahan saat menyimpan data:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.mom.formSubmit') }}" id="momForm" novalidate>
                @csrf
                <input type="hidden" name="unit" value="{{ $unit }}">
                <input type="hidden" name="owner_username" value="{{ $username }}">

                {{-- Header Card --}}
                <div class="form-header-card">
                    <h1>To do List</h1>
                    <p>[Notulensi Rapat]</p>
                    <p>Silakan isi detail rapat serta poin-poin tindak lanjut (to do list) di bawah ini. Anda dapat menambahkan beberapa poin rapat sekaligus.</p>
                </div>

                {{-- Container untuk Point/Card dinamis --}}
                <div id="points-container">
                    {{-- Data lama dari Validation Redirect jika ada --}}
                    @if(old('points'))
                        @foreach(old('points') as $idx => $point)
                            <div class="point-card animate-slide-in" data-index="{{ $idx }}">
                                <div class="point-card-header">
                                    <span class="point-number">Poin Rapat #{{ $idx + 1 }}</span>
                                    <button type="button" class="btn-remove-card" title="Hapus poin ini" onclick="removeCard(this)">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">ToDoList / Poin Tindak Lanjut <span class="req">*</span></label>
                                    <textarea name="points[{{ $idx }}][keterangan]" class="field-textarea" rows="3" 
                                        placeholder="Tuliskan keterangan poin atau to-do list..." required>{{ $point['keterangan'] }}</textarea>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Deadline</label>
                                    <input type="date" name="points[{{ $idx }}][deadline]" class="field-input" 
                                        value="{{ $point['deadline'] }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Default card pertama --}}
                        <div class="point-card animate-slide-in" data-index="0">
                            <div class="point-card-header">
                                <span class="point-number">Poin Rapat #1</span>
                                <button type="button" class="btn-remove-card" title="Hapus poin ini" onclick="removeCard(this)">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                            <div class="field-group">
                                <label class="field-label">ToDoList / Poin Tindak Lanjut <span class="req">*</span></label>
                                <textarea name="points[0][keterangan]" class="field-textarea" rows="3"
                                    placeholder="Tuliskan keterangan poin atau to-do list..." required></textarea>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Deadline</label>
                                <input type="date" name="points[0][deadline]" class="field-input">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Centered Add Point Button -->
                <div class="add-point-container" style="display: flex; justify-content: center; margin: 24px 0 12px 0;">
                    <button type="button" class="btn-add-circle" id="btn-add-point" title="Tambah Poin Rapat Baru">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                </div>

                {{-- Action Toolbar --}}
                <div class="footer-actions" style="justify-content: flex-end;">
                    <div class="actions-right">
                        @if (Auth::check())
                            <a href="{{ route('admin.mom.index', ['unit' => $unit]) }}" class="btn btn-cancel">Batal</a>
                        @else
                            <a href="javascript:history.back()" class="btn btn-cancel">Batal</a>
                        @endif
                        <button type="submit" class="btn btn-primary" id="btn-submit-form">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            <span>Simpan</span>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <script>
        // Auto-dismiss flash notifications after 5 seconds
        const successFlash = document.getElementById('flashSuccess');
        if (successFlash) {
            setTimeout(function() {
                successFlash.style.transition = 'opacity .4s, transform .4s';
                successFlash.style.opacity = '0';
                successFlash.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    successFlash.remove();
                }, 400);
            }, 5000);
        }

        // Points Container & Card Management
        const container = document.getElementById('points-container');
        const btnAddPoint = document.getElementById('btn-add-point');
        const momForm = document.getElementById('momForm');
        const submitBtn = document.getElementById('btn-submit-form');

        function reindexCards() {
            const cards = container.querySelectorAll('.point-card');
            cards.forEach((card, index) => {
                card.setAttribute('data-index', index);
                card.querySelector('.point-number').textContent = `Poin Rapat #${index + 1}`;
                
                // Update input name attributes
                const textarea = card.querySelector('textarea');
                if (textarea) textarea.setAttribute('name', `points[${index}][keterangan]`);
                
                const dateInput = card.querySelector('input[type="date"]');
                if (dateInput) dateInput.setAttribute('name', `points[${index}][deadline]`);
                
                // Hide delete button if only 1 card left
                const deleteBtn = card.querySelector('.btn-remove-card');
                if (deleteBtn) {
                    if (cards.length === 1) {
                        deleteBtn.style.display = 'none';
                    } else {
                        deleteBtn.style.display = 'flex';
                    }
                }
            });
        }

        function createCard() {
            const cards = container.querySelectorAll('.point-card');
            const newIndex = cards.length;
            
            const cardHtml = `
                <div class="point-card animate-slide-in" data-index="${newIndex}">
                    <div class="point-card-header">
                        <span class="point-number">Poin Rapat #${newIndex + 1}</span>
                        <button type="button" class="btn-remove-card" title="Hapus poin ini" onclick="removeCard(this)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                    <div class="field-group">
                        <label class="field-label">ToDoList / Poin Tindak Lanjut <span class="req">*</span></label>
                        <textarea name="points[${newIndex}][keterangan]" class="field-textarea" rows="3"
                            placeholder="Tuliskan keterangan poin atau to-do list..." required></textarea>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Deadline</label>
                        <input type="date" name="points[${newIndex}][deadline]" class="field-input">
                    </div>
                </div>
            `;
            
            // Append and scroll smoothly
            container.insertAdjacentHTML('beforeend', cardHtml);
            reindexCards();
            
            const newCard = container.querySelector(`[data-index="${newIndex}"]`);
            newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Focus on new textarea
            setTimeout(() => {
                newCard.querySelector('textarea').focus();
            }, 100);
        }

        window.removeCard = function(button) {
            const card = button.closest('.point-card');
            const cards = container.querySelectorAll('.point-card');
            if (cards.length <= 1) return;

            card.classList.remove('animate-slide-in');
            card.classList.add('animate-fade-out');

            setTimeout(() => {
                card.remove();
                reindexCards();
            }, 300);
        };

        // Event Listeners
        btnAddPoint.addEventListener('click', createCard);

        momForm.addEventListener('submit', function() {
            // Disable button to prevent double-submit and show loading text
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.querySelector('span').textContent = 'Menyimpan...';
        });

        // Initialize first card state
        reindexCards();
    </script>
</body>

</html>
