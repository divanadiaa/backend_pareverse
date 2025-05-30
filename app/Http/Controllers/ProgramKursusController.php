<?php

namespace App\Http\Controllers;

use App\Models\ProgramKursus;
use Illuminate\Http\Request;

class ProgramKursusController extends Controller
{
    /**
     * Ambil semua data program kursus
     * Route: GET /program-kursuses
     */
    public function index()
    {
        try {
            $programKursuses = ProgramKursus::with('lembaga')->get();
            
            return response()->json([
                'message' => 'Data program kursus berhasil diambil',
                'data' => $programKursuses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data program kursus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil detail satu program kursus
     * Route: GET /program-kursuses/{id}
     */
    public function show($id)
    {
        try {
            $programKursus = ProgramKursus::with('lembaga')->findOrFail($id);
            
            return response()->json([
                'message' => 'Detail program kursus berhasil diambil',
                'data' => $programKursus
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Program kursus tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil detail program kursus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buat program kursus baru
     * Route: POST /program-kursuses
     */
    public function store(Request $request)
    {
        try {
            // Validasi data yang dikirim
            $validated = $request->validate([
                'lembaga_id' => 'required|exists:lembagas,id',
                'nama_program' => 'required|string|max:255',
                'bahasa' => 'required|string|max:100',
                'harga' => 'required|numeric|min:0',
                'durasi' => 'required|string|max:100'
            ]);

            // Buat program kursus baru
            $programKursus = ProgramKursus::create($validated);

            // Load relasi lembaga
            $programKursus->load('lembaga');

            return response()->json([
                'message' => 'Program kursus berhasil ditambahkan',
                'data' => $programKursus
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Data tidak valid',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menambahkan program kursus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update data program kursus
     * Route: PUT /program-kursuses/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            // Cari program kursus berdasarkan ID
            $programKursus = ProgramKursus::findOrFail($id);

            // Validasi data yang dikirim
            $validated = $request->validate([
                'lembaga_id' => 'sometimes|required|exists:lembagas,id',
                'nama_program' => 'sometimes|required|string|max:255',
                'bahasa' => 'sometimes|required|string|max:100',
                'harga' => 'sometimes|required|numeric|min:0',
                'durasi' => 'sometimes|required|string|max:100'
            ]);

            // Update data program kursus
            $programKursus->update($validated);

            // Load relasi lembaga dan refresh data
            $programKursus = $programKursus->fresh('lembaga');

            return response()->json([
                'message' => 'Program kursus berhasil diperbarui',
                'data' => $programKursus
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Program kursus tidak ditemukan'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Data tidak valid',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memperbarui program kursus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus data program kursus
     * Route: DELETE /program-kursuses/{id}
     */
    public function destroy($id)
    {
        try {
            // Cari program kursus berdasarkan ID
            $programKursus = ProgramKursus::findOrFail($id);

            // Hapus program kursus
            $programKursus->delete();

            return response()->json([
                'message' => 'Program kursus berhasil dihapus'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Program kursus tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menghapus program kursus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil semua program kursus berdasarkan lembaga
     * Route: GET /program-kursuses/lembaga/{lembaga_id}
     */
    public function getByLembaga($lembaga_id)
    {
        try {
            $programKursuses = ProgramKursus::where('lembaga_id', $lembaga_id)
                ->with('lembaga')
                ->get();

            if ($programKursuses->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada program kursus untuk lembaga ini',
                    'data' => []
                ]);
            }

            return response()->json([
                'message' => 'Data program kursus berhasil diambil',
                'data' => $programKursuses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil program kursus: ' . $e->getMessage()
            ], 500);
        }
    }
}