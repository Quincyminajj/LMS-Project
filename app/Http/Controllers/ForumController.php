<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Kelas;
use Illuminate\Http\Request;

class ForumController extends Controller
{

    public function index($kelas_id)
    {
        $kelas = Kelas::with('guru')->findOrFail($kelas_id);
        $forums = Forum::where('kelas_id', $kelas_id)
            ->with('komentars')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('forum.index', compact('kelas', 'forums'));
    }

    public function all()
    {
        $forums = Forum::with('komentars')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($forums);
    }

    public function show($id)
    {
        $forum = Forum::with(['komentars', 'kelas'])->findOrFail($id);
        return view('forum.show', compact('forum'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        // Ambil nama user dari session
        $validated['dibuat_oleh'] = session('user_name') ?? session('identifier');

        $forum = Forum::create($validated);

        return redirect()->route('kelas.forum', $validated['kelas_id'])->with('success', 'Forum berhasil dibuat');
    }

    public function create($kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        return view('forum.create', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $forum = Forum::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $forum->update($validated);

        return redirect()->route('forum.show', $id)->with('success', 'Forum berhasil diperbarui');
    }

    public function destroy($id)
    {
        $forum = Forum::findOrFail($id);
        $kelas_id = $forum->kelas_id;
        $forum->delete();

        return redirect()->route('kelas.forum', $kelas_id)->with('success', 'Forum berhasil dihapus');
    }
}
