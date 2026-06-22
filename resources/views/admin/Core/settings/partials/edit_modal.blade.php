<div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-left">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit User - {{ $u->name }}
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('admin.settings.users.update', $u->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group font-weight-bold">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                    </div>
                    <div class="form-group font-weight-bold">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                    </div>
                    <div class="form-group font-weight-bold">
                        <label>Role</label>
                        <select name="role" class="form-control role-select" required>
                            @if (!in_array($u->role, $roles))
                                <option value="{{ $u->role }}" selected>
                                    @if ($u->role == 'cs-mbc')
                                        CS MBC
                                    @elseif($u->role == 'cs-smi')
                                        CS SMI
                                    @else
                                        {{ ucfirst($u->role) }}
                                    @endif
                                </option>
                            @endif
                            @foreach ($roles as $r)
                                <option value="{{ $r }}" {{ $u->role == $r ? 'selected' : '' }}>
                                    @if ($r == 'cs-mbc')
                                        CS MBC
                                    @elseif($r == 'cs-smi')
                                        CS SMI
                                    @else
                                        {{ ucfirst($r) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group font-weight-bold">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                            <option value="Pusat" {{ ($u->kategori ?? 'Pusat') === 'Pusat' ? 'selected' : '' }}>Pusat
                            </option>
                            <option value="Cabang" {{ ($u->kategori ?? '') === 'Cabang' ? 'selected' : '' }}>Cabang
                            </option>
                            <option value="Agen Pusat" {{ ($u->kategori ?? '') === 'Agen Pusat' ? 'selected' : '' }}>Agen Pusat
                            </option>
                        </select>
                    </div>
                    <div class="form-group chapter-field-container font-weight-bold"
                        style="display: {{ in_array($u->role, ['chapter', 'reseller', 'agen']) ? 'block' : 'none' }};">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0">{{ ($u->kategori ?? '') === 'Agen Pusat' ? 'Asal Kota' : 'Pilih Chapter' }}</label>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-add-chapter-toggle"
                                style="font-size: 0.65rem; padding: 2px 8px; display: {{ ($u->kategori ?? '') === 'Agen Pusat' ? 'none' : 'inline-block' }};">
                                <i class="fas fa-plus mr-1"></i>Tambah Chapter
                            </button>
                        </div>

                        <div class="chapter-select-wrapper" style="display: {{ ($u->kategori ?? '') === 'Agen Pusat' ? 'none' : 'block' }};">
                            <select {{ ($u->kategori ?? '') === 'Agen Pusat' ? '' : 'name=chapter' }} class="form-control chapter-select"
                                data-current="{{ $u->chapter }}">
                                <option value="">-- Pilih / Tulis Chapter --</option>
                                @foreach ($takenChapters as $chap)
                                    <option value="{{ $chap }}"
                                        {{ ($u->chapter ?? '') == $chap ? 'selected' : '' }}>
                                        {{ $chap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="city-input-wrapper" style="display: {{ ($u->kategori ?? '') === 'Agen Pusat' ? 'block' : 'none' }};">
                            <input type="text" {{ ($u->kategori ?? '') === 'Agen Pusat' ? 'name=chapter' : '' }} class="form-control city-input" value="{{ $u->chapter }}" placeholder="Tulis Asal Kota...">
                        </div>

                        <div class="chapter-input-wrapper mt-1" style="display: none;">
                            <div class="input-group">
                                <input type="text" class="form-control new-chapter-input"
                                    placeholder="Tulis Wilayah Chapter Baru...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm btn-secondary btn-cancel-new-chapter">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-info mt-1 d-block font-weight-normal" style="font-size: 0.7rem;"><i
                                    class="fas fa-info-circle mr-1"></i>Chapter baru akan otomatis tersimpan saat user
                                disimpan.</small>
                        </div>
                    </div>
                    @if(!$isOperasional)
                    {{-- Named Sub-Roles (hanya untuk CS-MBC) --}}
                    <div class="form-group font-weight-bold named-subrole-section" style="display: {{ strtolower($u->role) === 'cs-mbc' ? 'block' : 'none' }};">
                        <label class="mb-1"><i class="fas fa-layer-group mr-1 text-primary"></i>Sub-Role CS-MBC</label>
                        <small class="form-text text-muted mb-2">Pilih satu atau lebih sub-role. Permission otomatis diaktifkan.</small>
                        @foreach($subroleMap as $subName => $flags)
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" name="subrole[]" value="{{ $subName }}" class="custom-control-input" id="subrole_{{ $subName }}_{{ $u->id }}" {{ $u->hasSubrole($subName) ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="subrole_{{ $subName }}_{{ $u->id }}">
                                {{ strtoupper($subName) }}
                            </label>
                            <div class="text-muted" style="font-size: 0.75rem; margin-left: 1.5rem;">
                                @php
                                    $menuLabels = [
                                        'hrd' => 'SDM, Keuangan Kecil, Program Kerja',
                                        'keuangan' => 'Keuangan Besar, Penarikan Dompet, SPP Peserta, SDM, Program Kerja',
                                    ];
                                @endphp
                                {{ $menuLabels[$subName] ?? implode(', ', $flags) }}
                            </div>
                        </div>
                        @endforeach
                        <hr class="my-3">
                        <small class="text-muted">Izin detail (otomatis dari sub-role):</small>
                    </div>
                    @endif
                    <div class="form-group font-weight-bold">
                        <label>Password (Kosongkan jika tidak diganti)</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning font-weight-bold">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    // Toggle named-subrole-section based on role dropdown in edit modal
    var modal = document.getElementById('editUserModal{{ $u->id }}');
    if (!modal) return;
    var roleSelect = modal.querySelector('.role-select');
    if (!roleSelect) return;
    
    function toggleSubroleSection() {
        var isCsMbc = roleSelect.value === 'cs-mbc';
        var namedSection = modal.querySelector('.named-subrole-section');
        if (namedSection) namedSection.style.display = isCsMbc ? 'block' : 'none';
    }
    
    roleSelect.addEventListener('change', toggleSubroleSection);
    toggleSubroleSection();
})();
</script>
