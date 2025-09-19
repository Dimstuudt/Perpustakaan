<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    // Menampilkan daftar kategori (hanya yang aktif / belum dihapus)
    // CategoryController.php
public function index()
{
    $categories = Category::withCount('books')
        ->latest()
        ->paginate(10);

    return Inertia::render('Categories/Index', [
        'categories' => $categories,
    ]);
}


    // Menampilkan form tambah kategori
    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    // Menampilkan form edit kategori
    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
        ]);
    }

    // Update data kategori
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui');
    }

    // Soft delete kategori
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus (soft delete)');
    }

    // Menampilkan kategori yang sudah dihapus (trash)
    public function trashed()
    {
        $categories = Category::onlyTrashed()->latest()->paginate(10);

        return Inertia::render('Categories/Trashed', [
            'categories' => $categories,
        ]);
    }

    // Restore kategori yang terhapus
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('categories.trashed')->with('success', 'Kategori berhasil dikembalikan');
    }

    // Hapus permanen kategori
    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();

        return redirect()->route('categories.trashed')->with('success', 'Kategori berhasil dihapus permanen');
    }

    // Bulk hapus kategori (soft delete)
public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []);

    if (!empty($ids)) {
        Category::whereIn('id', $ids)->delete();
    }

    // Redirect ke halaman index seperti normal
    return redirect()->route('categories.index')
        ->with('success', 'Kategori berhasil dihapus.');
}

// Bulk restore
public function bulkRestore(Request $request)
{
    Category::withTrashed()->whereIn('id', $request->ids)->restore();
    return back()->with('success', 'Kategori berhasil direstore.');
}


// Bulk force delete
public function bulkForceDelete(Request $request)
{
    Category::withTrashed()->whereIn('id', $request->ids)->forceDelete();
    return back()->with('success', 'Kategori dihapus permanen.');
}

}
