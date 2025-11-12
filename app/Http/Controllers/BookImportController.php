<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Book;
use Inertia\Inertia;
use App\Models\Category;

class BookImportController extends Controller
{
  public function index()
{
    $categories = \App\Models\Category::select('id', 'name')->get();

    return Inertia::render('Import/Books', [
        'categories' => $categories
    ]);
}


  public function store(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv',
    ]);

    $savedBooks = [];

    // PRIORITAS 1: Gunakan data yang sudah dinormalisasi dari frontend
    if ($request->has('books_data') && $request->books_data) {
        $books = json_decode($request->books_data, true);

        foreach ($books as $bookData) {
            // Validasi unik ISBN sebelum simpan
            if (Book::where('isbn', $bookData['isbn'])->exists()) {
                continue; // skip jika ISBN sudah ada
            }

            // Pastikan category_id valid
            $categoryId = $bookData['category_id'];
            if (!Category::where('id', $categoryId)->exists()) {
                // Cari kategori "default"
                $defaultCategory = Category::where('name', 'default')->first();
                $categoryId = $defaultCategory ? $defaultCategory->id : null;
            }

            // Normalize type
            $type = $this->normalizeType($bookData['type'] ?? 'physical');

            $savedBooks[] = Book::create([
                'isbn'        => $bookData['isbn'] ?? null,
                'title'       => $bookData['title'] ?? null,
                'author'      => $bookData['author'] ?? null,
                'publisher'   => $bookData['publisher'] ?? null,
                'year'        => $bookData['year'] ?? null,
                'pages'       => $bookData['pages'] ?? null,
                'description' => $bookData['description'] ?? null,
                'category_id' => $categoryId,
                'type'        => $type, // ✅ Normalized
                'stock'       => $bookData['stock'] ?? 0,
                'fee'         => $bookData['fee'] ?? 0,
                'cover_path'  => 'images/dummy-cover.png',
                'file_path'   => null,
            ]);
        }
    }
    // FALLBACK: Baca langsung dari Excel (jika books_data tidak ada)
    else {
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        // Cari kategori default sekali saja
        $defaultCategory = Category::where('name', 'default')->first();

        foreach ($sheetData as $index => $row) {
            if ($index === 0) continue; // skip header

            // Validasi unik ISBN sebelum simpan
            if (Book::where('isbn', $row[0])->exists()) {
                continue; // skip jika ISBN sudah ada
            }

            // Cek apakah category_id valid
            $categoryId = $row[7] ?? null;
            if ($categoryId && !Category::where('id', $categoryId)->exists()) {
                // Gunakan kategori default jika invalid
                $categoryId = $defaultCategory ? $defaultCategory->id : null;
            }

            // Normalize type
            $type = $this->normalizeType($row[8] ?? 'physical');

            // Map Excel kolom ke field Book
            $bookData = [
                'isbn'        => $row[0] ?? null,
                'title'       => $row[1] ?? null,
                'author'      => $row[2] ?? null,
                'publisher'   => $row[3] ?? null,
                'year'        => $row[4] ?? null,
                'pages'       => $row[5] ?? null,
                'description' => $row[6] ?? null,
                'category_id' => $categoryId,
                'type'        => $type, // ✅ Normalized
                'stock'       => $row[9] ?? 0,
                'fee'         => $row[10] ?? 0,
                'cover_path'  => 'images/dummy-cover.png',
                'file_path'   => null,
            ];

            $savedBooks[] = Book::create($bookData);
        }
    }

    return redirect()->back()
        ->with('success', count($savedBooks) . ' buku berhasil diimport!');
}

/**
 * Normalize book type to valid values
 *
 * @param string $type
 * @return string
 */
private function normalizeType($type)
{
    // Convert to lowercase and trim
    $normalized = strtolower(trim($type));

    // Map common variations to 'physical'
    $physicalVariations = ['physical', 'phy', 'fisik', 'p', 'buku fisik'];
    if (in_array($normalized, $physicalVariations)) {
        return 'physical';
    }

    // Map common variations to 'digital'
    $digitalVariations = ['digital', 'dig', 'e-book', 'ebook', 'd', 'elektronik'];
    if (in_array($normalized, $digitalVariations)) {
        return 'digital';
    }

    // Default fallback
    return 'physical';
}
}
