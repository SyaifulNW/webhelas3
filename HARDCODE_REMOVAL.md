# Perbandingan Sebelum & Sesudah Penghapusan Hardcode

## Ringkasan

Semua logika akses kontrol yang sebelumnya menggunakan **nama orang** (string matching) atau **user ID** (angka statis) telah diganti menjadi **permission-based** menggunakan `hasHakAkses()` / `hasAnyHakAkses()`.

---

## 1. database.blade.php

**File:** `resources/views/admin/Sales/database/database.blade.php`

### Perubahan 1 — Tombol "Tambah Data" (Baris ~1124)

```php
// ❌ SEBELUM (hardcode nama "Rafi")
@if (
    !in_array($userRole, ['administrator', 'manager', 'marketing']) &&
    !($userRole === 'operasional' && stripos(auth()->user()->name, 'Rafi') === false) &&
    !((auth()->user()->hasHakAkses('spp_admin')) && request('view') !== 'me'))
```

```php
// ✅ SESUDAH (permission-based)
@if (
    !in_array($userRole, ['administrator', 'manager', 'marketing']) &&
    !($userRole === 'operasional' && !auth()->user()->hasHakAkses('operasional_rafi')) &&
    !((auth()->user()->hasHakAkses('spp_admin')) && request('view') !== 'me'))
```

### Perubahan 2 — Tombol "Lihat Jadwal Zoom" (Baris ~1153)

```php
// ❌ SEBELUM (hardcode nama "Rafi")
@if (
    !in_array($userRole, ['chapter', 'reseller', 'agen']) &&
    !(auth()->user()->role === 'operasional' && stripos(auth()->user()->name, 'Rafi') !== false) &&
    !($userRole === 'administrator' && request('view_type') == 'chapter'))
```

```php
// ✅ SESUDAH (permission-based)
@if (
    !in_array($userRole, ['chapter', 'reseller', 'agen']) &&
    !(auth()->user()->role === 'operasional' && auth()->user()->hasHakAkses('operasional_rafi')) &&
    !($userRole === 'administrator' && request('view_type') == 'chapter'))
```

---

## 2. row_chapter.blade.php

**File:** `resources/views/admin/Sales/database/partials/row_chapter.blade.php`

### Perubahan — Hak Edit di Chapter View (Baris ~22)

```php
// ❌ SEBELUM (hardcode nama "Rafi")
$authUser = auth()->user();
$userRole = strtolower($authUser->role);
// Rafi (operasional) diberi hak edit di chapter view
$isRafi = ($userRole === 'operasional' && stripos($authUser->name, 'Rafi') !== false);
$canEdit = !in_array($userRole, ['marketing', 'administrator', 'operasional']) || $isRafi;
```

```php
// ✅ SESUDAH (permission-based)
$authUser = auth()->user();
$userRole = strtolower($authUser->role);
// Operasional dengan hak akses operasional_rafi diberi hak edit di chapter view
$isRafi = ($userRole === 'operasional' && $authUser->hasHakAkses('operasional_rafi'));
$canEdit = !in_array($userRole, ['marketing', 'administrator', 'operasional']) || $isRafi;
```

---

## 3. salesplan/index.blade.php

**File:** `resources/views/admin/Sales/salesplan/index.blade.php`

### Perubahan 1 — Admin View Flag (Baris ~386)

```php
// ❌ SEBELUM (hardcode user ID 1 dan 13)
$isAdminView = (
    strtolower(auth()->user()->role) === 'administrator' ||
    auth()->user()->hasAnyHakAkses(['spp_admin', 'sales_admin', 'cs_supervisor', 'sales_full_view']) ||
    auth()->id() == 1 ||
    auth()->id() == 13
);
```

```php
// ✅ SESUDAH (permission-based)
$isAdminView = (
    strtolower(auth()->user()->role) === 'administrator' ||
    auth()->user()->hasAnyHakAkses([
        'spp_admin', 'sales_admin', 'cs_supervisor',
        'sales_full_view', 'sales_all_view', 'young_startup_admin'
    ])
);
```

### Perubahan 2 — Filter CS per Tim (Baris ~502)

```php
// ❌ SEBELUM (hardcode user ID 1 dan 13)
@if(
    strtolower(auth()->user()->role) === 'administrator' ||
    auth()->user()->hasAnyHakAkses(['spp_admin', 'sales_admin', 'cs_supervisor', 'sales_full_view']) ||
    (auth()->id() == 1) ||
    ((auth()->user()->hasHakAkses('young_startup_admin') || auth()->id() == 13) && $cs->hasHakAkses('young_startup_cs'))
)
```

```php
// ✅ SESUDAH (permission-based)
@if(
    strtolower(auth()->user()->role) === 'administrator' ||
    auth()->user()->hasAnyHakAkses(['spp_admin', 'sales_admin', 'cs_supervisor', 'sales_full_view', 'sales_all_view']) ||
    (auth()->user()->hasHakAkses('young_startup_admin') && $cs->hasHakAkses('young_startup_cs'))
)
```

### Perubahan 3 — Filter Kelas Visibility (Baris ~513)

```php
// ❌ SEBELUM (hardcode user ID 13)
@if(!(auth()->user()->hasHakAkses('young_startup_admin') || auth()->id() == 13))
```

```php
// ✅ SESUDAH (permission-based)
@if(!auth()->user()->hasHakAkses('young_startup_admin'))
```

### Perubahan 4 — Filter Kelas Options (Baris ~524-525)

```php
// ❌ SEBELUM (hardcode user ID 1 dan 13)
@if(
    ((strtolower(auth()->user()->role) === 'administrator' || auth()->id() == 1)
        && !in_array($kelas->nama_kelas, ['Start-Up Muda Indonesia', 'Sekolah Kaya', 'Start-Up Muslim Indonesia'])) ||
    ((auth()->user()->hasHakAkses('young_startup_admin') || auth()->id() == 13)
        && $kelas->nama_kelas == 'Start-Up Muda Indonesia') ||
    ...
)
```

```php
// ✅ SESUDAH (permission-based)
@if(
    ((strtolower(auth()->user()->role) === 'administrator' || auth()->user()->hasHakAkses('sales_all_view'))
        && !in_array($kelas->nama_kelas, ['Start-Up Muda Indonesia', 'Sekolah Kaya', 'Start-Up Muslim Indonesia'])) ||
    (auth()->user()->hasHakAkses('young_startup_admin')
        && $kelas->nama_kelas == 'Start-Up Muda Indonesia') ||
    ...
)
```

---

## Mapping Permission yang Digunakan

| Hardcode Lama | Permission Pengganti | Keterangan |
|---|---|---|
| `stripos(name, 'Rafi')` | `hasHakAkses('operasional_rafi')` | Akses operasional khusus (edit chapter view, tombol tambah) |
| `auth()->id() == 1` | `hasHakAkses('sales_all_view')` | Super-admin / akses penuh semua data |
| `auth()->id() == 13` | `hasHakAkses('young_startup_admin')` | Admin khusus kelas Start-Up Muda Indonesia |

---

## Catatan Penting

Pastikan user-user berikut sudah memiliki permission yang sesuai di kolom `hak_akses`:

| User (sebelumnya di-hardcode) | Permission yang harus ada |
|---|---|
| User ID 1 (Linda) | `sales_all_view`, `spp_admin`, `cs_supervisor` |
| User ID 13 | `young_startup_admin` |
| Rafi (operasional) | `operasional_rafi` |

Jika belum, tambahkan melalui **Settings > Users & Roles > Edit User > Sub-Role CS-MBC** atau langsung di database:

```sql
-- Contoh menambahkan hak_akses operasional_rafi ke user Rafi
UPDATE users SET hak_akses = JSON_ARRAY_APPEND(COALESCE(hak_akses, JSON_ARRAY()), '$', 'operasional_rafi') WHERE id = <user_id>;
```

---

## Manfaat Perubahan Ini

1. **Tidak bergantung pada nama/ID spesifik** — jika user berganti, tidak perlu ubah kode
2. **Lebih mudah dikelola** — semua akses diatur terpusat melalui permission flags
3. **Konsisten** — seluruh codebase menggunakan pola yang sama (`hasHakAkses`)
4. **Lebih aman** — tidak ada "magic number" atau string yang tersembunyi di logic
