<?php

namespace App\Http\Controllers;

use App\Models\ForumKomentar;
use App\Models\Forum;
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
            'isi' => 'required|string',
            'parent_id' => 'nullable|exists:forum_komentars,id',
        ]);

        // Ambil nama user dari session
        $validated['dibuat_oleh'] = session('user_name');
        $validated['pengirim_nisn_nip'] = session('identifier');

        ForumKomentar::create($validated);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $komentar = ForumKomentar::findOrFail($id);

        // Cek apakah user adalah pemilik komentar
        if ($komentar->pengirim_nisn_nip !== session('identifier')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit komentar ini');
        }

        $validated = $request->validate([
            'isi' => 'required|string',
        ]);

        $komentar->update($validated);

        return redirect()->route('forum.show', $komentar->forum_id)->with('success', 'Komentar berhasil diperbarui');
    }

    public function destroy($id)
    {
        $komentar = ForumKomentar::findOrFail($id);

        // Cek apakah user adalah pemilik komentar atau guru
        if ($komentar->pengirim_nisn_nip !== session('identifier') && session('role') !== 'guru') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus komentar ini');
        }

        $forum_id = $komentar->forum_id;
        $komentar->delete();

        return redirect()->route('forum.show', $forum_id)->with('success', 'Komentar berhasil dihapus');
    }
}
