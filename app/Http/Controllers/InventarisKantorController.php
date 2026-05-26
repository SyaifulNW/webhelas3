<?php

namespace App\Http\Controllers;

use App\Models\InventarisKantor;
use Illuminate\Http\Request;

class InventarisKantorController extends Controller
{
    public function store(Request $request)
    {
        $item = InventarisKantor::create([
            'lokasi' => $request->lokasi ?? '',
            'nama_peralatan' => $request->nama_peralatan ?? '',
            'jumlah' => $request->jumlah ?? '',
            'status' => $request->status ?? 'Normal'
        ]);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = InventarisKantor::findOrFail($id);
        
        // Handle checkbox
        if ($request->has('ceklist_perbaikan')) {
            $data = $request->all();
            $data['ceklist_perbaikan'] = $request->ceklist_perbaikan == 'true' || $request->ceklist_perbaikan == 1 ? 1 : 0;
            $item->update($data);
        } else {
            $item->update($request->all());
        }
        
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        InventarisKantor::destroy($id);
        return response()->json(['success' => true]);
    }
}
