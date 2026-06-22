<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'label', 'is_active'];

    // Helper to check if active
    public static function isActive($name) {
        $userId = auth()->check() ? auth()->user()->id : 'guest';
        $version = \Cache::get('menu_cache_version', 1);
        $cacheKey = "menu_active_{$name}_{$userId}_v{$version}";

        return \Cache::remember($cacheKey, 3600, function() use ($name) {
            $menu = self::where('name', $name)->first();
            if ($menu) {
                // Special case for settings menu for finance_access
                if (in_array($name, ['settings', 'keuangan_besar']) && auth()->check() && auth()->user()->hasHakAkses('finance_access')) {
                    return true;
                }

                // Special case for keuangan_kecil
                if ($name === 'keuangan_kecil' && auth()->check() && auth()->user()->hasAnyHakAkses(['finance_kecil', 'cs_supervisor'])) {
                    return true;
                }
                
                // GLOBAL override if menu is disabled globally
                if (!$menu->is_active) {
                    return false;
                }

                // Role based check
                if (auth()->check()) {
                    try {
                        $role = strtolower(trim(auth()->user()->role));
                        $roleMenu = \DB::table('role_menus')->where('role', $role)
                                    ->where('menu_id', $menu->id)
                                    ->first();
                        if ($roleMenu) {
                            return (bool)$roleMenu->can_access;
                        }
                    } catch (\Exception $e) {
                        // Ignore if table missing
                    }
                }

                // Fallback to global setting
                return (bool)$menu->is_active;
            }
            return true; // Default true if not found in menus table
        });
    }

    public static function hasRoleAccess($menuName, $role) {
        try {
            $menu = self::where('name', $menuName)->first();
            if (!$menu) return true;

            $roleMenu = \DB::table('role_menus')->where('role', $role)
                        ->where('menu_id', $menu->id)
                        ->first();
            
            return $roleMenu ? $roleMenu->can_access : true; // Default allow if not set
        } catch (\Exception $e) {
            return true; // Table missing
        }
    }
}
