<?php

namespace App\Http\Controllers;

use App\Models\BookmarkLembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkLembagaController extends Controller
{
    /**
     * Menampilkan daftar bookmark milik pengguna yang sedang login.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookmarks()
    {
        $bookmarks = BookmarkLembaga::where('user_id', Auth::id())
            ->with('lembaga') // Memuat relasi lembaga
            ->get();

        return response()->json([
            'message' => 'Daftar bookmark berhasil diambil',
            'data' => $bookmarks
        ], 200);
    }

    /**
     * Menampilkan detail bookmark berdasarkan ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $bookmark = BookmarkLembaga::where('user_id', Auth::id())
            ->with('lembaga')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail bookmark berhasil diambil',
            'data' => $bookmark
        ], 200);
    }

    /**
     * Membuat bookmark baru untuk lembaga.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookmarkLembaga(Request $request)
    {
        $request->validate([
            'lembaga_id' => 'required|exists:lembagas,id',
        ]);

        // Cek apakah bookmark sudah ada
        $existingBookmark = BookmarkLembaga::where('user_id', Auth::id())
            ->where('lembaga_id', $request->lembaga_id)
            ->first();

        if ($existingBookmark) {
            return response()->json([
                'error' => 'Lembaga sudah dibookmark'
            ], 400);
        }

        $bookmark = BookmarkLembaga::create([
            'user_id' => Auth::id(),
            'lembaga_id' => $request->lembaga_id,
        ]);

        // Muat relasi lembaga untuk respons
        $bookmark->load('lembaga');

        return response()->json([
            'message' => 'Bookmark berhasil dibuat',
            'data' => $bookmark
        ], 201);
    }

    /**
     * Menghapus bookmark berdasarkan ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBookmark($id)
    {
        $bookmark = BookmarkLembaga::where('user_id', Auth::id())
            ->findOrFail($id);

        $bookmark->delete();

        return response()->json([
            'message' => 'Bookmark berhasil dihapus'
        ], 200);
    }

    /**
     * Memeriksa apakah lembaga sudah dibookmark oleh pengguna.
     *
     * @param int $lembagaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function check($lembagaId)
    {
        try {
            $isBookmarked = BookmarkLembaga::where('user_id', Auth::id())
                ->where('lembaga_id', $lembagaId)
                ->exists();

            return response()->json([
                'message' => 'Status bookmark berhasil diambil',
                'isBookmarked' => $isBookmarked
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memeriksa status bookmark: ' . $e->getMessage()
            ], 500);
        }
    }
}