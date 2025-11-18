<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Kelas;
use Illuminate\Http\Request;

class ForumController extends Controller
{

    public function index($kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        $forums = Forum::where('kelas_id', $kelas_id)
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
        $forum = Forum::with('komentars')->findOrFail($id);
        return response()->json($forum);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $validated['dibuat_oleh'] = auth()->user()->guru;

        $forum = Forum::create($validated);

        return redirect()->route('kelas.forum', $validated['kelas_id']) ->with('success', 'Forum berhasil dibuat');
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
            'kelas_id' => 'sometimes|exists:kelas,id',
            'judul' => 'sometimes|string|max:255',
            'isi' => 'sometimes|string',
            'dibuat_oleh' => 'sometimes|string|max:20',
        ]);

        $forum->update($validated);
        return response()->json($forum);
    }

    public function destroy($id)
    {
        $forum = Forum::findOrFail($id);
        $forum->delete();

        return response()->json(['message' => 'Forum berhasil dihapus']);
    }
}
