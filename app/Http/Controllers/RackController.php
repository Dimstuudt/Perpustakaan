<?php
namespace App\Http\Controllers;

use App\Models\Rack;
use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RackController extends Controller
{
    public function index(Request $request)
{
    $racks = Rack::with('books')
        ->when($request->search, fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('Rack/Index', [
        'racks' => $racks,
        'filters' => $request->only(['search']),
    ]);
}


    public function create()
    {
        return Inertia::render('Rack/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        Rack::create($request->only('name','code','description'));

        return redirect()->route('racks.index')->with('message', 'Rack created successfully!');
    }

public function edit(Rack $rack)
{
    // Buku bebas, hanya untuk dropdown assign
    $books = Book::whereNull('rack_id')->get();

    // Pastikan rack bawa relasi books
    $rack->load('books');

    return Inertia::render('Rack/Edit', [
        'rack' => $rack,
        'books' => $books,
    ]);
}




    public function update(Request $request, Rack $rack)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'book_ids' => 'array',
    ]);

    $rack->update(['name' => $request->name]);

    // Kalau ada request untuk remove 1 buku
    if ($request->filled('remove_id')) {
        Book::where('id', $request->remove_id)
            ->where('rack_id', $rack->id)
            ->update(['rack_id' => null]);

        return redirect()->back()->with('message', 'Book removed from rack.');
    }

    // Assign buku baru ke rak (tidak reset semua lagi)
    if ($request->book_ids) {
        Book::whereIn('id', $request->book_ids)->update(['rack_id' => $rack->id]);
    }

    return redirect()->route('racks.index')->with('message', 'Rack updated successfully!');
}


    public function destroy(Rack $rack)
    {
        $rack->delete();
        return redirect()->route('racks.index')->with('message', 'Rack deleted successfully!');
    }
}
