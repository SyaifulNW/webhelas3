<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use App\Models\Mom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MomController extends Controller
{
    /**
     * Valid unit values.
     */
    const UNITS = ['Helas Corp', 'Helas Aesthetic Clinic'];

    /**
     * Roles that are blocked from MoM entirely (enforced at both route and controller level).
     */
    const BLOCKED_ROLES = ['chapter', 'reseller', 'agen'];

    /**
     * Roles with CS-level access (full edit).
     * Administrator is handled separately (read-only, all units, all data).
     */
    const MULTI_UNIT_ROLES = ['cs-mbc', 'cs-smi'];

    /**
     * User names (exact match) that may access the Helas Aesthetic Clinic panel.
     * All other CS users only see Helas Corp.
     */
    const CLINIC_UNIT_NAMES = ['Yasmin'];

    /**
     * Resolve MoM permissions for the currently authenticated user.
     *
     * Returns:
     *   canAccessUnits  – array of unit names the user may view
     *   canEdit         – bool: may add / update / delete rows
     *   isReadOnly      – bool: view-only (no mutations)
     *   seeAllData      – bool: sees all created_by rows (admin only)
     */
    public static function getMomPermissions(): array
    {
        $user = Auth::user();
        $role = strtolower(trim($user->role ?? ''));
        $username = strtolower(trim($user->username ?? ''));
        $name = strtolower(trim($user->name ?? ''));
        $isYasmin = ($username === 'yasmin' || $name === 'yasmin');

        // Blocked roles – should never reach here if route middleware is applied
        if (in_array($role, self::BLOCKED_ROLES) || str_starts_with($role, 'chapter_')) {
            return [
                'canAccessUnits' => [],
                'canEdit'        => false,
                'isReadOnly'     => true,
                'seeAllData'     => false,
            ];
        }

        // Administrator – all units, read-only, sees ALL data across all users
        if ($role === 'administrator') {
            return [
                'canAccessUnits' => self::UNITS,
                'canEdit'        => false,
                'isReadOnly'     => true,
                'seeAllData'     => true,
            ];
        }

        // CS roles – full edit, own data only
        // Clinic panel only for users listed in CLINIC_UNIT_NAMES (e.g. Yasmin)
        if (in_array($role, self::MULTI_UNIT_ROLES)) {
            $canAccessClinic = in_array($user->name, self::CLINIC_UNIT_NAMES) || $isYasmin;
            return [
                'canAccessUnits' => $canAccessClinic ? self::UNITS : ['Helas Corp'],
                'canEdit'        => true,
                'isReadOnly'     => false,
                'seeAllData'     => $isYasmin ? true : false,
            ];
        }

        // All other internal staff – Helas Corp only, full edit, own data only
        return [
            'canAccessUnits' => ['Helas Corp'],
            'canEdit'        => true,
            'isReadOnly'     => false,
            'seeAllData'     => $isYasmin ? true : false,
        ];
    }

    /**
     * Display a listing of the MoM records.
     */
    public function index(Request $request)
    {
        $permissions = self::getMomPermissions();

        // Hard block – return 403 for blocked roles
        if (empty($permissions['canAccessUnits'])) {
            abort(403, 'Akses ditolak.');
        }

        // Clamp requested unit to what the user may access
        $unit = $request->get('unit', $permissions['canAccessUnits'][0]);
        if (!in_array($unit, $permissions['canAccessUnits'])) {
            $unit = $permissions['canAccessUnits'][0];
        }

        // Auto-update overdue records for this unit
        Mom::where('unit', $unit)
            ->where('status', '!=', 'Done')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now()->toDateString())
            ->update(['status' => 'Overdue']);

        $query = Mom::where('unit', $unit);

        // Non-admin users only see their own data
        if (!$permissions['seeAllData']) {
            $query->where('created_by', Auth::id());
        }

        // Optional status filter
        if ($request->has('status') && $request->status !== 'all' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Optional deadline filter
        if ($request->filled('deadline_filter')) {
            $query->whereDate('deadline', $request->deadline_filter);
        }

        // For admin: eager-load creator and group by user
        if ($permissions['seeAllData']) {
            $query->with('creator');
        }

        // Sort by deadline (default to asc: closest deadline first)
        $sortDeadline = $request->get('sort_deadline', 'asc');
        if ($sortDeadline === 'asc') {
            $query->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline asc');
        } elseif ($sortDeadline === 'desc') {
            $query->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline desc');
        } else {
            $query->orderBy('tanggal', 'desc')
                  ->orderBy('created_at', 'desc');
        }

        $moms = $query->get();

        // Group by creator for admin view
        $groupedMoms = null;
        if ($permissions['seeAllData']) {
            $groupedMoms = $moms->groupBy('created_by');
        }

        $pics = \App\Models\User::where('kategori', 'Pusat')
            ->where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $moms,
                'grouped' => $permissions['seeAllData'],
            ]);
        }

        return view('admin.Operations.mom.index', compact('moms', 'unit', 'permissions', 'groupedMoms', 'pics'));
    }

    /**
     * Show the standalone form for creating a new MoM record (Helas Corp only).
     * URL: /admin/mom/form-{username} — publicly accessible, username is slugified name/username.
     */
    public function create(Request $request, string $username)
    {
        $owner = \App\Models\User::get()->first(function($u) use ($username) {
            return \Illuminate\Support\Str::slug($u->name) === $username || \Illuminate\Support\Str::slug($u->username) === $username;
        });

        if (!$owner) {
            abort(404, 'User tidak ditemukan.');
        }

        $role = strtolower(trim($owner->role ?? ''));
        if (in_array($role, self::BLOCKED_ROLES) || str_starts_with($role, 'chapter_')) {
            abort(403, 'Akses ditolak.');
        }

        // Form is always for Helas Corp — no unit in URL needed
        $unit = 'Helas Corp';

        return view('admin.Operations.mom.form-mom', compact('unit', 'owner', 'username'));
    }

    /**
     * Handle form submission from the standalone create form.
     */
    public function submitForm(Request $request)
    {
        $ownerUsername = $request->input('owner_username');
        if (!$ownerUsername) {
            return redirect()->back()->withErrors(['error' => 'Validasi error: pemilik form tidak ditentukan.']);
        }

        $owner = \App\Models\User::get()->first(function($u) use ($ownerUsername) {
            return \Illuminate\Support\Str::slug($u->name) === $ownerUsername || \Illuminate\Support\Str::slug($u->username) === $ownerUsername;
        });

        if (!$owner) {
            return redirect()->back()->withErrors(['error' => 'User tidak ditemukan.']);
        }

        $role = strtolower(trim($owner->role ?? ''));
        if (in_array($role, self::BLOCKED_ROLES) || str_starts_with($role, 'chapter_')) {
            return redirect()->back()->withErrors(['error' => 'Akses ditolak.']);
        }

        $unit = $request->input('unit', 'Helas Corp');

        if ($unit !== 'Helas Corp') {
            return redirect()->back()->withErrors(['error' => 'Form input hanya tersedia untuk MoM Helas Corp.']);
        }

        $validated = $request->validate([
            'points'              => 'required|array|min:1',
            'points.*.keterangan' => 'required|string',
            'points.*.deadline'   => 'nullable|date',
        ]);

        foreach ($validated['points'] as $point) {
            Mom::create([
                'tanggal'    => now()->toDateString(),
                'pic'        => $owner->name,
                'target'     => '',
                'keterangan' => $point['keterangan'],
                'deadline'   => $point['deadline'] ?? null,
                'status'     => 'Progress',
                'unit'       => $unit,
                'created_by' => $owner->id,
            ]);
        }

        return redirect()
            ->route('admin.mom.create', ['username' => $ownerUsername])
            ->with('success', 'Data MoM berhasil disimpan.');
    }

    /**
     * Store a newly created MoM record (AJAX).
     * Unit is taken from the active panel sent by the frontend – validated server-side.
     */
    public function store(Request $request)
    {
        $permissions = self::getMomPermissions();

        if (!$permissions['canEdit']) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // Take unit from request, clamp to what the user is allowed
        $unit = $request->get('unit', $permissions['canAccessUnits'][0] ?? 'Helas Corp');
        if (!in_array($unit, $permissions['canAccessUnits'])) {
            $unit = $permissions['canAccessUnits'][0] ?? 'Helas Corp';
        }

        $mom = Mom::create([
            'tanggal'    => now()->toDateString(),
            'keterangan' => '',
            'deadline'   => null,
            'pic'        => '',
            'target'     => '',
            'hasil'      => '',
            'status'     => 'Progress',
            'unit'       => $unit,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $mom,
        ]);
    }

    /**
     * Update a MoM record (AJAX).
     */
    public function update(Request $request, $id)
    {
        $permissions = self::getMomPermissions();

        if (!$permissions['canEdit']) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $mom = Mom::findOrFail($id);

        // Must belong to an accessible unit
        if (!in_array($mom->unit, $permissions['canAccessUnits'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // Non-admin may only edit their own records
        if (!$permissions['seeAllData'] && $mom->created_by !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'tanggal'    => 'nullable|date',
            'keterangan' => 'nullable|string',
            'deadline'   => 'nullable|date',
            'pic'        => 'nullable|string|max:255',
            'target'     => 'nullable|string',
            'hasil'      => 'nullable|string',
            'status'     => 'nullable|in:Progress,Done,Overdue',
        ]);

        foreach ($validated as $field => $value) {
            if ($request->has($field)) {
                $mom->{$field} = $value;
            }
        }

        $mom->save();

        return response()->json([
            'success' => true,
            'data'    => $mom,
        ]);
    }

    /**
     * Delete a MoM record (AJAX).
     */
    public function destroy($id)
    {
        $permissions = self::getMomPermissions();

        if (!$permissions['canEdit']) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $mom = Mom::findOrFail($id);

        // Must belong to an accessible unit
        if (!in_array($mom->unit, $permissions['canAccessUnits'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // Non-admin may only delete their own records
        if (!$permissions['seeAllData'] && $mom->created_by !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $mom->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data MoM berhasil dihapus.',
        ]);
    }
}
