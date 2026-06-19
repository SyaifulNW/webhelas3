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
                    <div class="form-group font-weight-bold">
                        <label>Sub-Roles (Akses Khusus)</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="cs_supervisor" class="custom-control-input" id="subrole_supervisor_{{ $u->id }}" {{ $u->hasSubrole('cs_supervisor') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_supervisor_{{ $u->id }}">CS Supervisor (Akses Admin)</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="sales_all_view" class="custom-control-input" id="subrole_sales_all_{{ $u->id }}" {{ $u->hasSubrole('sales_all_view') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_sales_all_{{ $u->id }}">Sales All View (Exempt CS)</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="gantt_cross_view" class="custom-control-input" id="subrole_gantt_{{ $u->id }}" {{ $u->hasSubrole('gantt_cross_view') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_gantt_{{ $u->id }}">Gantt Cross View</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="finance_access" class="custom-control-input" id="subrole_finance_{{ $u->id }}" {{ $u->hasSubrole('finance_access') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_finance_{{ $u->id }}">Akses Keuangan Besar</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="finance_kecil" class="custom-control-input" id="subrole_finance_kecil_{{ $u->id }}" {{ $u->hasSubrole('finance_kecil') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_finance_kecil_{{ $u->id }}">Akses Keuangan Kecil</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="clinic_access" class="custom-control-input" id="subrole_clinic_{{ $u->id }}" {{ $u->hasSubrole('clinic_access') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_clinic_{{ $u->id }}">Akses Klinik (MoM)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="cs_pusat" class="custom-control-input" id="subrole_cs_pusat_{{ $u->id }}" {{ $u->hasSubrole('cs_pusat') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_cs_pusat_{{ $u->id }}">CS Pusat</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="cs_rotasi" class="custom-control-input" id="subrole_cs_rotasi_{{ $u->id }}" {{ $u->hasSubrole('cs_rotasi') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_cs_rotasi_{{ $u->id }}">CS Rotasi Lead</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="exempt_transfer" class="custom-control-input" id="subrole_exempt_{{ $u->id }}" {{ $u->hasSubrole('exempt_transfer') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_exempt_{{ $u->id }}">Exempt Transfer (CEO/Owner)</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="subrole[]" value="operasional_rafi" class="custom-control-input" id="subrole_rafi_{{ $u->id }}" {{ $u->hasSubrole('operasional_rafi') ? 'checked' : '' }}>
                                    <label class="custom-control-label font-weight-normal" for="subrole_rafi_{{ $u->id }}">Operasional (Rafi)</label>
                                </div>
                            </div>
                        </div>
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
