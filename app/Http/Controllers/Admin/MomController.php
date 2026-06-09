<?php

namespace App\Http\Controllers\Admin;

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
     * Display a listing of the MoM records.
     */
    public function index(Request $request)
    {
        $unit = $request->get('unit', 'Helas Corp');

        // Validate unit
        if (!in_array($unit, self::UNITS)) {
            $unit = 'Helas Corp';
        }

        $query = Mom::where('unit', $unit);

        // Filter based on status if requested and not 'all'
        if ($request->has('status') && $request->status !== 'all' && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Ordered by tanggal desc, then created_at desc (default newest first)
        $moms = $query->orderBy('tanggal', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $moms
            ]);
        }

        return view('admin.mom.index', compact('moms', 'unit'));
    }

    /**
     * Store a newly created MoM record in database (AJAX).
     */
    public function store(Request $request)
    {
        $unit = $request->get('unit', 'Helas Corp');

        // Validate unit
        if (!in_array($unit, self::UNITS)) {
            $unit = 'Helas Corp';
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
            'data'    => $mom
        ]);
    }

    /**
     * Update the specified MoM record in database (AJAX).
     */
    public function update(Request $request, $id)
    {
        $mom = Mom::findOrFail($id);

        $validated = $request->validate([
            'tanggal'    => 'nullable|date',
            'keterangan' => 'nullable|string',
            'deadline'   => 'nullable|date',
            'pic'        => 'nullable|string|max:255',
            'target'     => 'nullable|string',
            'hasil'      => 'nullable|string',
            'status'     => 'nullable|in:Progress,Done,Overdue',
        ]);

        // Dynamically update fields that are present in the request
        foreach ($validated as $field => $value) {
            if ($request->has($field)) {
                $mom->{$field} = $value;
            }
        }

        $mom->save();

        return response()->json([
            'success' => true,
            'data'    => $mom
        ]);
    }

    /**
     * Remove the specified MoM record from database (AJAX).
     */
    public function destroy($id)
    {
        $mom = Mom::findOrFail($id);
        $mom->delete();

        return response()->json([
            'success'  => true,
            'message'  => 'Data MoM berhasil dihapus.'
        ]);
    }
}
