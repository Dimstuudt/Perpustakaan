<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Book;
use Inertia\Inertia;

class BookImportController extends Controller
{
    public function index()
    {
        return Inertia::render('Import/Books'); // halaman Vue
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $savedBooks = [];

        foreach ($sheetData as $index => $row) {
            if ($index === 0) continue; // skip header

            // Map Excel kolom ke field Book
            $bookData = [
                'isbn'        => $row[0] ?? null,
                'title'       => $row[1] ?? null,
                'author'      => $row[2] ?? null,
                'publisher'   => $row[3] ?? null,
                'year'        => $row[4] ?? null,
                'pages'       => $row[5] ?? null,
                'description' => $row[6] ?? null,
                'category_id' => $row[7] ?? null,
                'type'        => $row[8] ?? 'physical',
                'stock'       => $row[9] ?? 0,
                'fee'         => $row[10] ?? 0,
                'cover_path'  => 'images/dummy-cover.png', // default
                'file_path'   => null, // bisa diisi kalau ada file terpisah
            ];

            // Validasi unik ISBN sebelum simpan
            if (Book::where('isbn', $bookData['isbn'])->exists()) {
                continue; // skip jika ISBN sudah ada
            }

            $savedBooks[] = Book::create($bookData);
        }

        return redirect()->back()
            ->with('success', count($savedBooks) . ' buku berhasil diimport!');
    }
}
