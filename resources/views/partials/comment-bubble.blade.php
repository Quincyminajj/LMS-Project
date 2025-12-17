@php
    $isOwnComment = session('user_name') === $komentar->dibuat_oleh;
    $canEdit = session('user_name') === $komentar->dibuat_oleh;
    $canDelete = session('user_name') === $komentar->dibuat_oleh || 
                 (session('role') === 'guru' && session('identifier') === $forum->kelas->guru_nip);
    
    // Deteksi role user yang membuat komentar
    // Cek apakah NIP guru (biasanya format berbeda dengan NISN)
    $isGuru = strlen($komentar->pengirim_nisn_nip) < 10 || 
              $komentar->pengirim_nisn_nip === $forum->kelas->guru_nip;
    $userRole = $isGuru ? 'Guru' : 'Siswa';
    $badgeClass = $isGuru ? 'badge-guru' : 'badge-siswa';
@endphp

<div class="comment-bubble {{ $isOwnComment ? 'own-comment' : 'other-comment' }}">
    <div class="bubble-content {{ $isOwnComment ? 'own' : 'other' }}">
        @if(!$isOwnComment)
            <div class="comment-author">
                {{ $komentar->dibuat_oleh }}
                <span class="user-badge {{ $badgeClass }}">
                    <i class="bi bi-{{ $isGuru ? 'mortarboard-fill' : 'person-fill' }}"></i>
                    {{ $userRole }}
                </span>
            </div>
        @else
            <div class="comment-author">
                Anda
                <span class="user-badge" style="background: rgba(255,255,255,0.3); color: white;">
                    <i class="bi bi-{{ $isGuru ? 'mortarboard-fill' : 'person-fill' }}"></i>
                    {{ $userRole }}
                </span>
            </div>
        @endif

        {{-- Preview komentar yang dibalas --}}
        @if($komentar->parent_id && $komentar->parent)
            <div class="reply-reference {{ $isOwnComment ? 'own-reply-ref' : 'other-reply-ref' }}">
                <i class="bi bi-reply-fill"></i>
                <div class="reply-ref-content">
                    <strong>{{ $komentar->parent->dibuat_oleh }}</strong>
                    <p>{{ Str::limit($komentar->parent->isi, 60) }}</p>
                </div>
            </div>
        @endif
        
        <div class="comment-text">{{ $komentar->isi }}</div>
        
        <div class="comment-actions">
            <div class="comment-time">
                {{ $komentar->created_at->diffForHumans() }}
            </div>
            
            <button type="button" class="btn btn-reply btn-sm" onclick="toggleReplyForm({{ $komentar->id }}, '{{ addslashes($komentar->dibuat_oleh) }}', '{{ addslashes($komentar->isi) }}')">
                <i class="bi bi-reply"></i> Balas
            </button>
            
            @if($canEdit || $canDelete)
                <div class="dropdown d-inline">
                    <button class="btn btn-sm btn-reply dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if($canEdit)
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                   data-bs-target="#editModal{{ $komentar->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </li>
                        @endif
                        @if($canDelete)
                            <li>
                                <form action="{{ route('forum-komentar.destroy', $komentar->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus komentar ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Replies -->
@if($komentar->replies->count() > 0)
    <div class="reply-section">
        @foreach($komentar->replies()->orderBy('created_at', 'asc')->get() as $reply)
            @include('partials.comment-bubble', ['komentar' => $reply, 'forum' => $forum])
        @endforeach
    </div>
@endif

<!-- Edit Modal -->
<div class="modal fade" id="editModal{{ $komentar->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Komentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('forum-komentar.update', $komentar->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <textarea name="isi" class="form-control" rows="4" required>{{ $komentar->isi }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>