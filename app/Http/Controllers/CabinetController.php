<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Rack;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CabinetController extends Controller
{
  public function index(Request $request)
{
    $perPage = $request->get('per_page', 10);

    $cabinets = Cabinet::withCount('racks')
        ->when($request->search, fn($q, $search) =>
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
        )
        ->paginate($perPage)
        ->withQueryString();

    return Inertia::render('Cabinets/Index', [
        'cabinets' => $cabinets,
        'filters' => $request->only('search'),
    ]);
}

    public function create()
    {
        return Inertia::render('Cabinets/Create');
    }

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

    public function edit(Cabinet $cabinet)
    {
        // Racks yang belum punya cabinet
        $racks = Rack::whereNull('cabinet_id')->get(['id', 'name']);

        // Load racks yang sudah punya cabinet ini
        $cabinet->load('racks');

        return Inertia::render('Cabinets/Edit', [
            'cabinet' => $cabinet,
            'racks' => $racks,
        ]);
    }

    public function update(Request $request, Cabinet $cabinet)
    {
        $request->validate([
            'code' => 'required|string|max:100|unique:cabinets,code,' . $cabinet->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rack_ids' => 'array',
        ]);

        // Update data utama
        $cabinet->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // 🔹 Jika ada request untuk remove satu rack
        if ($request->filled('remove_id')) {
            Rack::where('id', $request->remove_id)
                ->where('cabinet_id', $cabinet->id)
                ->update(['cabinet_id' => null]);

            return redirect()->back()->with('message', 'Rack removed from cabinet.');
        }

        // 🔹 Jika ada racks baru yang mau ditambahkan
        if ($request->rack_ids) {
            Rack::whereIn('id', $request->rack_ids)
                ->update(['cabinet_id' => $cabinet->id]);
        }

        return redirect()->route('cabinets.index')
            ->with('message', 'Cabinet updated successfully!');
    }

    public function destroy(Cabinet $cabinet)
    {
        // kosongkan racks yang ada di cabinet ini
        Rack::where('cabinet_id', $cabinet->id)->update(['cabinet_id' => null]);
        $cabinet->delete();

        return redirect()->route('cabinets.index')
            ->with('message', 'Cabinet deleted successfully!');
    }
}
