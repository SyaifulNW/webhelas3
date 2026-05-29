<div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-left">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit User - {{ $u->name }}</h5>
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
                            @foreach($roles as $r)
                                <option value="{{ $r }}" {{ $u->role == $r ? 'selected' : '' }}>
                                    @if($r == 'cs-mbc') CS MBC
                                    @elseif($r == 'cs-smi') CS SMI
                                    @else {{ ucfirst($r) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group chapter-field-container font-weight-bold"
                        style="display: {{ in_array($u->role, ['chapter', 'reseller']) ? 'block' : 'none' }};">
                        
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0">Pilih Chapter</label>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-add-chapter-toggle" style="font-size: 0.65rem; padding: 2px 8px;">
                                <i class="fas fa-plus mr-1"></i>Tambah Chapter
                            </button>
                        </div>
                        
                        <div class="chapter-select-wrapper">
                            <select name="chapter" class="form-control chapter-select" data-current="{{ $u->chapter }}">
                                <option value="">-- Pilih / Tulis Chapter --</option>
                                @foreach($takenChapters as $chap)
                                    <option value="{{ $chap }}" {{ ($u->chapter ?? '') == $chap ? 'selected' : '' }}>
                                        {{ $chap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="chapter-input-wrapper mt-1" style="display: none;">
                            <div class="input-group">
                                <input type="text" class="form-control new-chapter-input" placeholder="Tulis Wilayah Chapter Baru...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm btn-secondary btn-cancel-new-chapter">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-info mt-1 d-block font-weight-normal" style="font-size: 0.7rem;"><i class="fas fa-info-circle mr-1"></i>Chapter baru akan otomatis tersimpan saat user disimpan.</small>
                        </div>
                    </div>
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