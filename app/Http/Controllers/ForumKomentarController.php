<?php

namespace App\Http\Controllers;

use App\Models\ForumKomentar;
use Illuminate\Http\Request;

class ForumKomentarController extends Controller
{
    public function index()
    {
        $komentars = ForumKomentar::with('children')->orderBy('created_at', 'asc')->get();
        return response()->json($komentars);
    }

    public function show($id)
    {
        $komentar = ForumKomentar::with('children')->findOrFail($id);
        return response()->json($komentar);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'forum_id' => 'required|exists:forums,id',
            'pengirim_nisn_nip' => 'required|string|max:20',
            'pengirim_tipe' => 'required|in:siswa,guru',
            'isi' => 'required|string',
            'parent_id' => 'nullable|exists:forum_komentars,id',
            'created_at' => 'nullable|date',
        ]);
        
        $validated['dibuat_oleh'] = auth()->user()->username;
        $komentar = ForumKomentar::create($validated);
        return response()->json($komentar, 201);
    }

    public function update(Request $request, $id)
    {
        $komentar = ForumKomentar::findOrFail($id);

        $validated = $request->validate([
            'isi' => 'sometimes|string',
        ]);

        $komentar->update($validated);
        return response()->json($komentar);
    }

    public function destroy($id)
    {
        $komentar = ForumKomentar::findOrFail($id);
        $komentar->delete();
        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }
}
