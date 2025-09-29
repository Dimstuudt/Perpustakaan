<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Rack;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CabinetController extends Controller
{
    // List all cabinets
    public function index(Request $request)
    {
        $cabinets = Cabinet::withCount('racks')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Cabinets/Index', [
            'cabinets' => $cabinets,
            'filters' => $request->only('search'),
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('Cabinets/Create');
    }

    // Store new cabinet
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:cabinets,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Cabinet::create($validated);

        return redirect()->route('cabinets.index')
            ->with('success', 'Cabinet created successfully.');
    }

    // Show edit form
  public function edit(Cabinet $cabinet)
{
    // load racks yang sudah dimiliki cabinet ini (buat list di bawah)
    $cabinet->load('racks:id,name,cabinet_id');

    // racks yang masih available (belum punya cabinet sama sekali)
    $racks = \App\Models\Rack::whereNull('cabinet_id')
        ->get(['id', 'name']);

    return Inertia::render('Cabinets/Edit', [
        'cabinet' => $cabinet,
        'racks'   => $racks,
    ]);
}


    // Update cabinet
    public function update(Request $request, Cabinet $cabinet)
    {
        $validated = $request->validate([
            'code' => 'required|unique:cabinets,code,' . $cabinet->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rack_ids' => 'array',
            'rack_ids.*' => 'exists:racks,id',
        ]);

        // update data utama
        $cabinet->update($validated);

        // kalau ada rack_ids dikirim, update relasi racks
        if ($request->has('rack_ids')) {
            // reset racks lama -> set cabinet_id = null
            Rack::where('cabinet_id', $cabinet->id)->update(['cabinet_id' => null]);

            // assign racks baru
            Rack::whereIn('id', $request->rack_ids)->update(['cabinet_id' => $cabinet->id]);
        }

        return redirect()->route('cabinets.index')
            ->with('success', 'Cabinet updated successfully.');
    }

    // Delete cabinet
    public function destroy(Cabinet $cabinet)
    {
        // opsional: kosongkan rack_id sebelum hapus cabinet
        Rack::where('cabinet_id', $cabinet->id)->update(['cabinet_id' => null]);

        $cabinet->delete();

        return redirect()->route('cabinets.index')
            ->with('success', 'Cabinet deleted successfully.');
    }
}
