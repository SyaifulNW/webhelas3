<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Sub-role → permission flag (hak_akses) mapping.
     * Setiap sub-role berisi kumpulan flag yang otomatis di-sync ke kolom hak_akses.
     */
    const SUBROLE_FLAGS = [
        'hrd' => ['cs_supervisor', 'gantt_cross_view', 'finance_kecil', 'cs_pusat', 'cs_rotasi'],
        'keuangan' => ['sales_all_view', 'finance_access', 'gantt_cross_view', 'cs_supervisor', 'cs_pusat', 'cs_rotasi'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'wa',
        'chapter',
        'id_no',
        'photo',
        'bio',
        'created_by',
        'is_active',
        'divisi',
        'tipe_kontrak',
        'status_sdm',
        'kategori',
        'hak_akses',
        'subrole',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'hak_akses' => 'array',
        'subrole' => 'array',
    ];

    // ─── Hak Akses (Permission Flags) Methods ───

    public function hasHakAkses(string $flag): bool
    {
        if (empty($this->hak_akses)) {
            return false;
        }
        $flags = is_array($this->hak_akses) ? $this->hak_akses : json_decode($this->hak_akses, true);
        return is_array($flags) && in_array($flag, $flags);
    }

    public function hasAnyHakAkses(array $flags): bool
    {
        if (empty($this->hak_akses)) {
            return false;
        }
        $userFlags = is_array($this->hak_akses) ? $this->hak_akses : json_decode($this->hak_akses, true);
        if (!is_array($userFlags)) {
            return false;
        }
        return count(array_intersect($flags, $userFlags)) > 0;
    }

    // ─── Sub-Role Methods ───

    /**
     * Set sub-roles dan otomatis sync permission flags ke hak_akses.
     */
    public function setSubroles(array $subroles): void
    {
        // Filter hanya sub-role yang valid
        $valid = array_intersect($subroles, array_keys(self::SUBROLE_FLAGS));
        $this->subrole = $valid;

        // Sync permission flags dari semua sub-role
        $this->syncHakAksesFlags();
    }

    /**
     * Get sub-roles yang dimiliki user.
     */
    public function getSubroles(): array
    {
        $subroles = $this->subrole;
        if (is_string($subroles)) {
            $subroles = json_decode($subroles, true);
        }
        return is_array($subroles) ? $subroles : [];
    }

    /**
     * Cek apakah user memiliki sub-role tertentu.
     */
    public function hasSubrole(string $name): bool
    {
        return in_array($name, $this->getSubroles());
    }

    /**
     * Sync permission flags (hak_akses) berdasarkan sub-roles yang aktif.
     * Menambahkan flag dari sub-role tanpa menghapus flag manual lainnya.
     */
    private function syncHakAksesFlags(): void
    {
        $currentFlags = is_array($this->hak_akses) ? $this->hak_akses : (json_decode($this->hak_akses, true) ?? []);

        // Tambahkan semua flag dari sub-roles
        foreach ($this->getSubroles() as $sub) {
            if (isset(self::SUBROLE_FLAGS[$sub])) {
                foreach (self::SUBROLE_FLAGS[$sub] as $flag) {
                    if (!in_array($flag, $currentFlags)) {
                        $currentFlags[] = $flag;
                    }
                }
            }
        }

        $this->hak_akses = $currentFlags;
    }

    public function isRole(string $role): bool
    {
        return strtolower($this->role) === strtolower($role);
    }

    public function isAnyRole(array $roles): bool
    {
        $userRole = strtolower($this->role);
        $allowedRoles = array_map('strtolower', $roles);
        return in_array($userRole, $allowedRoles);
    }

    // app/Models/User.php
    public function data()
    {
        return $this->hasMany(Data::class, 'created_by', 'name');
    }

    public function leads()
    {
        return $this->hasMany(Leads::class, 'created_by', 'name');
    }

    public function salesplans()
    {
        return $this->hasMany(SalesPlan::class, 'created_by');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function ensureWalletExists()
    {
        if (!$this->wallet) {
            $walletId = 'MBC-' . strtoupper(substr(uniqid(), -6));
            return Wallet::create([
                'user_id' => $this->id,
                'wallet_id' => $walletId,
                'balance' => 0,
                'status' => 'active'
            ]);
        }
        return $this->wallet;
    }
}
