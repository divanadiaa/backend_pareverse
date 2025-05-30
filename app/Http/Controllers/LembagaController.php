<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use Illuminate\Http\Request;

class LembagaController extends Controller
{
    /**
     * Ambil semua data lembaga
     * Route: GET /lembagas
     */
    public function getLembagas()
    {
        try {
            $lembagas = Lembaga::with('programKursuses')->get();
            return response()->json([
                'message' => 'Data lembaga berhasil diambil',
                'data' => $lembagas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data lembaga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil detail satu lembaga
     * Route: GET /lembagas/{id}
     */
    public function getLembagaDetail($id)
    {
        try {
            $lembaga = Lembaga::with('programKursuses')->findOrFail($id);
            return response()->json([
                'message' => 'Detail lembaga berhasil diambil',
                'data' => $lembaga
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lembaga tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Buat lembaga baru
     * Route: POST /lembagas
     */
    public function store(Request $request)
    {
        try {
            // Validasi data yang dikirim
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Gambar maks 2MB
                'alamat' => 'required|string',
                'link_maps' => 'nullable|url',
                'whatsapp' => 'required|string',
                'is_recommended' => 'boolean'
            ]);

            // Upload gambar jika ada
            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('lembagas', 'public');
                $validated['gambar'] = $path;
            }

            // Buat lembaga baru
            $lembaga = Lembaga::create($validated);

            // Kembalikan respons sukses
            return response()->json([
                'message' => 'Lembaga berhasil ditambahkan',
                'data' => $lembaga
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Data tidak valid',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menambahkan lembaga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update data lembaga
     * Route: PUT /lembagas/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            // Cari lembaga berdasarkan ID
            $lembaga = Lembaga::findOrFail($id);

            // Validasi data yang dikirim
            $validated = $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'deskripsi' => 'sometimes|required|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Gambar maks 2MB
                'alamat' => 'sometimes|required|string',
                'link_maps' => 'nullable|url',
                'whatsapp' => 'sometimes|required|string',
                'is_recommended' => 'boolean'
            ]);

            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('lembagas', 'public');
                $validated['gambar'] = $path;
            }

            // Update data lembaga
            $lembaga->update($validated);

            // Kembalikan respons sukses
            return response()->json([
                'message' => 'Lembaga berhasil diperbarui',
                'data' => $lembaga->fresh()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Lembaga tidak ditemukan'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Data tidak valid',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memperbarui lembaga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus data lembaga
     * Route: DELETE /lembagas/{id}
     */
    public function destroy($id)
    {
        try {
            // Cari lembaga berdasarkan ID
            $lembaga = Lembaga::findOrFail($id);

            // Hapus lembaga
            $lembaga->delete();

            // Kembalikan respons sukses
            return response()->json([
                'message' => 'Lembaga berhasil dihapus'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Lembaga tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menghapus lembaga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter lembaga berdasarkan parameter
     * Route: POST /lembagas/filter
     */
    public function filterLembagas(Request $request)
    {
        try {
            $query = Lembaga::with('programKursuses');

            // Filter berdasarkan bahasa
            if ($request->has('bahasa')) {
                $query->whereHas('programKursuses', function ($q) use ($request) {
                    $q->where('bahasa', $request->bahasa);
                });
            }

            // Filter berdasarkan range harga
            if ($request->has('min_harga') && $request->has('max_harga')) {
                $query->whereHas('programKursuses', function ($q) use ($request) {
                    $q->whereBetween('harga', [$request->min_harga, $request->max_harga]);
                });
            }

            // Filter berdasarkan durasi
            if ($request->has('durasi')) {
                $query->whereHas('programKursuses', function ($q) use ($request) {
                    $q->where('durasi', $request->durasi);
                });
            }

            // Filter berdasarkan nama lembaga (opsional)
            if ($request->has('nama')) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            }

            $lembagas = $query->get();

            return response()->json([
                'message' => 'Data lembaga berhasil difilter',
                'data' => $lembagas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memfilter lembaga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil lembaga yang direkomendasikan
     * Route: GET /lembagas/recommended
     */
    public function getRecommendedLembagas()
    {
        try {
            $lembagas = Lembaga::with('programKursuses')
                ->withAvg('reviews', 'rating')
                ->orderByDesc('is_recommended')
                ->orderByDesc('reviews_avg_rating')
                ->take(10)
                ->get();

            return response()->json([
                'message' => 'Data lembaga rekomendasi berhasil diambil',
                'data' => $lembagas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil lembaga rekomendasi: ' . $e->getMessage()
            ], 500);
        }
    }
}