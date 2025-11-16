<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::with('komentars')->orderBy('created_at', 'desc')->get();
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
            'dibuat_oleh' => 'required|string|max:20',
        ]);

        $forum = Forum::create($validated);
        return response()->json($forum, 201);
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
