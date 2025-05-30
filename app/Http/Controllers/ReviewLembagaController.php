<?php

namespace App\Http\Controllers;

use App\Models\ReviewLembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewLembagaController extends Controller
{
    /**
     * Ambil semua data review
     * Route: GET /reviews
     */
    public function index()
    {
        try {
            $reviews = ReviewLembaga::with(['user', 'lembaga'])->get();
            
            return response()->json([
                'message' => 'Data review berhasil diambil',
                'data' => $reviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil data review berdasarkan query lembaga_id
     * Route: GET /reviews?lembaga_id={id}
     */
    public function indexByLembaga(Request $request)
    {
        try {
            $lembagaId = $request->query('lembaga_id');
            if (!$lembagaId) {
                return response()->json([
                    'error' => 'Parameter lembaga_id diperlukan'
                ], 400);
            }

            $reviews = ReviewLembaga::where('lembaga_id', $lembagaId)
                ->with(['user', 'lembaga'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Data review berhasil diambil',
                'data' => $reviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil detail satu review
     * Route: GET /reviews/{id}
     */
    public function show($id)
    {
        try {
            $review = ReviewLembaga::with(['user', 'lembaga'])->findOrFail($id);
            
            return response()->json([
                'message' => 'Detail review berhasil diambil',
                'data' => $review
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Review tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil detail review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buat review baru
     * Route: POST /reviews
     */
    public function storeReview(Request $request)
    {
        try {
            // Validasi data yang dikirim
            $validated = $request->validate([
                'lembaga_id' => 'required|exists:lembagas,id',
                'rating' => 'required|integer|min:1|max:5',
                'komentar' => 'nullable|string|max:1000'
            ]);

            // Cek apakah user sudah memberikan review untuk lembaga ini
            $existingReview = ReviewLembaga::where('user_id', Auth::id())
                ->where('lembaga_id', $request->lembaga_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'error' => 'Anda sudah memberikan review untuk lembaga ini'
                ], 400);
            }

            // Buat review baru
            $review = ReviewLembaga::create([
                'user_id' => Auth::id(),
                'lembaga_id' => $validated['lembaga_id'],
                'rating' => $validated['rating'],
                'komentar' => $validated['komentar']
            ]);

            // Load relasi untuk response
            $review->load(['user', 'lembaga']);

            return response()->json([
                'message' => 'Review berhasil ditambahkan',
                'data' => $review
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Data tidak valid',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menambahkan review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update data review
     * Route: PUT /reviews/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            // Cari review berdasarkan ID
            $review = ReviewLembaga::findOrFail($id);

            // Cek apakah review milik user yang sedang login
            if ($review->user_id !== Auth::id()) {
                return response()->json([
                    'error' => 'Anda tidak memiliki akses untuk mengubah review ini'
                ], 403);
            }

            // Validasi data yang dikirim
            $validated = $request->validate([
                'rating' => 'sometimes|required|integer|min:1|max:5',
                'komentar' => 'nullable|string|max:1000'
            ]);

            // Update data review
            $review->update($validated);

            // Load relasi dan refresh data
            $review = $review->fresh(['user', 'lembaga']);

            return response()->json([
                'message' => 'Review berhasil diperbarui',
                'data' => $review
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Review tidak ditemukan'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Data tidak valid',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memperbarui review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus data review
     * Route: DELETE /reviews/{id}
     */
    public function destroy($id)
    {
        try {
            // Cari review berdasarkan ID
            $review = ReviewLembaga::findOrFail($id);

            // Cek apakah review milik user yang sedang login
            if ($review->user_id !== Auth::id()) {
                return response()->json([
                    'error' => 'Anda tidak memiliki akses untuk menghapus review ini'
                ], 403);
            }

            // Hapus review
            $review->delete();

            return response()->json([
                'message' => 'Review berhasil dihapus'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Review tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menghapus review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil review berdasarkan lembaga
     * Route: GET /reviews/lembaga/{lembaga_id} (jika diperlukan)
     */
    public function getByLembaga($lembaga_id)
    {
        try {
            $reviews = ReviewLembaga::where('lembaga_id', $lembaga_id)
                ->with(['user', 'lembaga'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Data review lembaga berhasil diambil',
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil review lembaga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil review berdasarkan user yang sedang login
     * Route: GET /reviews/my-reviews (jika diperlukan)
     */
    public function getMyReviews()
    {
        try {
            $reviews = ReviewLembaga::where('user_id', Auth::id())
                ->with(['user', 'lembaga'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Review Anda berhasil diambil',
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil review Anda: ' . $e->getMessage()
            ], 500);
        }
    }
}