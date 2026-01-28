@extends('layouts.app')

@section('title', $forum->judul)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Back Button -->
                <a href="{{ route('kelas.forum', $forum->kelas_id) }}"
                    class="text-decoration-none text-secondary mb-3 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Kembali ke Forum
                </a>

                <!-- Forum Post -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="fw-bold mb-2">{{ $forum->judul }}</h4>
                                <div class="text-muted small">
                                    <i class="bi bi-person-circle"></i> {{ $forum->dibuat_oleh }} •
                                    <i class="bi bi-clock"></i> {{ $forum->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>

                            @if (session('role') === 'guru' && session('identifier') === $forum->kelas->guru_nip)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form id="delete-forum-{{ $forum->id }}"
                                                action="{{ route('forum.destroy', $forum->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf @method('DELETE')
                                            </form>

                                            <button type="button" class="dropdown-item text-danger"
                                                onclick="confirmDeleteForum({{ $forum->id }})">
                                                Hapus Diskusi
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="border-top pt-3">
                            <p class="mb-3">{{ $forum->isi }}</p>

                            <!-- Tampilkan Gambar jika ada -->
                            @if ($forum->gambar)
                                <div class="mt-3">
                                    <img src="{{ asset('storage/forum_images/' . $forum->gambar) }}" alt="Forum Image"
                                        class="img-fluid rounded shadow-sm" style="max-width: 100%; cursor: pointer;"
                                        onclick="openImageModal(this.src)">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-chat-dots"></i> Komentar ({{ $forum->komentars->count() }})
                        </h5>

                        <!-- Comments List -->
                        <div class="comments-container mb-4" style="max-height: 600px; overflow-y: auto;">
                            @forelse($forum->komentars()->whereNull('parent_id')->orderBy('created_at', 'asc')->get() as $komentar)
                                @include('partials.comment-bubble', ['komentar' => $komentar, 'forum' => $forum])
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-chat-square-text fs-3"></i>
                                    <p class="mt-2">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Comment Form  -->
                        <div class="border-top pt-4">
                            <form id="commentForm" action="{{ route('forum-komentar.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                                <input type="hidden" id="parent_id" name="parent_id" value="">

                                <!-- Reply Preview -->
                                <div id="replyPreview" class="reply-preview d-none mb-2">
                                    <div class="reply-preview-header">
                                        <i class="bi bi-reply-fill"></i>
                                        <span>Membalas <strong id="replyToName"></strong></span>
                                        <button type="button" class="btn-cancel-reply" onclick="cancelReply()">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <div class="reply-preview-content" id="replyPreviewText">
                                        <!-- Isi komentar yang direply -->
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <textarea id="commentTextarea" name="isi" class="form-control" rows="3" placeholder="Tulis komentar Anda..." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> <span id="btnText">Kirim Komentar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk melihat gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <img id="modalImage" src="" alt="Full Image" class="img-fluid w-100">
                </div>
            </div>
        </div>
    </div>

    <style>
        .comment-bubble {
            max-width: 75%;
            margin-bottom: 1rem;
        }

        .comment-bubble.own-comment {
            margin-left: auto;
        }

        .comment-bubble.other-comment {
            margin-right: auto;
        }

        .bubble-content {
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
            word-break: break-word;
        }

        .bubble-content.own {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .bubble-content.other {
            background: #f1f3f5;
            color: #333;
            border-bottom-left-radius: 4px;
        }

        .comment-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 4px;
        }

        .comment-author {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 500;
            white-space: nowrap;
        }

        .badge-guru {
            background: #ffd700;
            color: #856404;
        }

        .badge-siswa {
            background: #e3f2fd;
            color: #1565c0;
        }

        .reply-section {
            margin-left: 40px;
            margin-top: 10px;
        }

        .reply-preview {
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            border-radius: 8px;
            padding: 10px 12px;
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reply-preview-header {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: #667eea;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .reply-preview-header i {
            font-size: 1rem;
        }

        .btn-cancel-reply {
            margin-left: auto;
            background: transparent;
            border: none;
            color: #999;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .btn-cancel-reply:hover {
            background: rgba(0,0,0,0.05);
            color: #333;
        }

        .reply-preview-content {
            font-size: 0.85rem;
            color: #666;
            padding: 6px 0;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .btn-reply {
            font-size: 0.8rem;
            padding: 4px 12px;
            border-radius: 12px;
            background: transparent;
            border: 1px solid rgba(0,0,0,0.1);
            color: #666;
            transition: all 0.2s;
        }

        .btn-reply:hover {
            background: rgba(0,0,0,0.05);
            color: #333;
        }

        .own .btn-reply {
            border-color: rgba(255,255,255,0.3);
            color: rgba(255,255,255,0.9);
        }

        .own .btn-reply:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .comment-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .comments-container::-webkit-scrollbar {
            width: 6px;
        }

        .comments-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .comments-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .comments-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Blur effect untuk background saat dropdown aktif */
        .chat-blur {
            filter: blur(3px);
            pointer-events: none;
            transition: filter 0.2s ease;
        }

        .dropdown-menu {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: none;
            border-radius: 8px;
        }

        .dropdown-item {
            padding: 8px 16px;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
        }

        /* Reply Reference Preview dalam Bubble Chat */
        .reply-reference {
            margin-bottom: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            display: flex;
            gap: 8px;
            align-items: start;
        }

        .reply-reference i {
            font-size: 0.9rem;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .other-reply-ref {
            background: rgba(0, 0, 0, 0.05);
            border-left: 3px solid #667eea;
        }

        .own-reply-ref {
            background: rgba(255, 255, 255, 0.15);
            border-left: 3px solid rgba(255, 255, 255, 0.5);
        }

        .reply-ref-content {
            flex: 1;
            min-width: 0;
        }

        .reply-ref-content strong {
            display: block;
            font-size: 0.8rem;
            margin-bottom: 2px;
            opacity: 0.9;
        }

        .reply-ref-content p {
            margin: 0;
            font-size: 0.8rem;
            opacity: 0.75;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.3;
        }

        /* RESPONSIVE MOBILE OPTIMIZATION */
        @media (max-width: 768px) {
            .container {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .col-lg-8 {
                padding-left: 0;
                padding-right: 0;
            }

            .card {
                border-radius: 0 !important;
                margin-bottom: 0 !important;
            }

            .card-body {
                padding: 12px !important;
            }

            /* Bubble chat mobile */
            .comment-bubble {
                max-width: 85%;
                margin-bottom: 0.75rem;
            }

            .bubble-content {
                padding: 10px 12px;
                font-size: 0.9rem;
            }

            .comment-author {
                font-size: 0.8rem;
            }

            .user-badge {
                font-size: 0.65rem;
                padding: 2px 6px;
            }

            .user-badge i {
                font-size: 0.7rem;
            }

            .comment-text {
                font-size: 0.9rem;
                line-height: 1.4;
            }

            .comment-actions {
                gap: 6px;
                margin-top: 4px;
            }

            .comment-time {
                font-size: 0.7rem;
            }

            .btn-reply {
                font-size: 0.75rem;
                padding: 3px 8px;
            }

            .btn-reply i {
                font-size: 0.8rem;
            }

            /* Reply section mobile */
            .reply-section {
                margin-left: 20px;
                margin-top: 8px;
            }

            /* Reply preview mobile */
            .reply-reference {
                padding: 6px 8px;
                margin-bottom: 6px;
            }

            .reply-reference i {
                font-size: 0.8rem;
            }

            .reply-ref-content strong {
                font-size: 0.75rem;
            }

            .reply-ref-content p {
                font-size: 0.75rem;
            }

            /* Comments container mobile */
            .comments-container {
                max-height: calc(100vh - 400px) !important;
                min-height: 300px;
            }

            /* Form komentar mobile */
            .border-top.pt-4 {
                padding-top: 12px !important;
                position: sticky;
                bottom: 0;
                background: white;
                z-index: 10;
                margin: 0 -12px;
                padding-left: 12px !important;
                padding-right: 12px !important;
                border-top: 2px solid #e9ecef !important;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            }

            #commentTextarea {
                font-size: 0.9rem;
                min-height: 80px;
            }

            .reply-preview {
                padding: 8px 10px;
                margin-bottom: 8px;
            }

            .reply-preview-header {
                font-size: 0.8rem;
            }

            .reply-preview-content {
                font-size: 0.8rem;
            }

            /* Forum post mobile */
            .card:first-child .card-body {
                padding-bottom: 12px !important;
            }

            .card:first-child h4 {
                font-size: 1.1rem;
            }

            .card:first-child .text-muted {
                font-size: 0.75rem;
            }

            /* Dropdown menu mobile */
            .dropdown-menu {
                font-size: 0.85rem;
            }

            .dropdown-item {
                padding: 6px 12px;
            }

            /* Modal mobile */
            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-body textarea {
                font-size: 0.9rem;
            }

            /* Hide scrollbar on mobile */
            .comments-container::-webkit-scrollbar {
                width: 3px;
            }

            /* Back button mobile */
            .mb-3.d-inline-block {
                margin-bottom: 8px !important;
                font-size: 0.9rem;
            }

            /* Button mobile */
            .btn-primary {
                font-size: 0.9rem;
                padding: 8px 16px;
            }
        }

        /* Extra small mobile (iPhone SE, etc) */
        @media (max-width: 375px) {
            .comment-bubble {
                max-width: 90%;
            }

            .bubble-content {
                padding: 8px 10px;
                font-size: 0.85rem;
            }

            .reply-section {
                margin-left: 15px;
            }

            .user-badge {
                font-size: 0.6rem;
                padding: 1px 5px;
            }

            .comment-actions {
                gap: 4px;
            }

            .btn-reply {
                font-size: 0.7rem;
                padding: 2px 6px;
            }
        }
    </style>

    <script>
        function confirmDeleteForum(id) {
            Swal.fire({
                title: 'Hapus Diskusi?',
                text: "Aksi ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-forum-${id}`).submit();
                }
            });
        }

        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }

        function toggleReplyForm(commentId, userName, commentText) {
            const parentIdInput = document.getElementById('parent_id');
            const textarea = document.getElementById('commentTextarea');
            const btnText = document.getElementById('btnText');
            const replyPreview = document.getElementById('replyPreview');
            const replyToName = document.getElementById('replyToName');
            const replyPreviewText = document.getElementById('replyPreviewText');
            
            // Jika sedang reply ke komentar yang sama, batalkan
            if (parentIdInput.value === commentId.toString()) {
                cancelReply();
                return;
            }
            
            // Set parent_id
            parentIdInput.value = commentId;
            
            // Update placeholder
            textarea.placeholder = 'Tulis balasan Anda...';
            textarea.focus();
            
            // Update button text
            btnText.textContent = 'Kirim Balasan';
            
            // Show reply preview
            replyPreview.classList.remove('d-none');
            replyToName.textContent = userName;
            replyPreviewText.textContent = commentText;
            
            // Scroll to form
            document.getElementById('commentForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function cancelReply() {
            const parentIdInput = document.getElementById('parent_id');
            const textarea = document.getElementById('commentTextarea');
            const btnText = document.getElementById('btnText');
            const replyPreview = document.getElementById('replyPreview');
            
            // Reset parent_id
            parentIdInput.value = '';
            
            // Reset placeholder
            textarea.placeholder = 'Tulis komentar Anda...';
            
            // Reset button text
            btnText.textContent = 'Kirim Komentar';
            
            // Hide reply preview
            replyPreview.classList.add('d-none');
        }

        // Auto scroll to bottom on page load
        document.addEventListener('DOMContentLoaded', function() {
            const commentsContainer = document.querySelector('.comments-container');
            if (commentsContainer) {
                commentsContainer.scrollTop = commentsContainer.scrollHeight;
            }
            
            // Reset form after submit
            const form = document.getElementById('commentForm');
            form.addEventListener('submit', function() {
                setTimeout(() => {
                    cancelReply();
                }, 100);
            });

            // Blur effect untuk dropdown
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                
                if (dropdownToggle) {
                    dropdownToggle.addEventListener('show.bs.dropdown', function() {
                        // Blur semua bubble chat kecuali yang aktif
                        const allBubbles = document.querySelectorAll('.comment-bubble');
                        const parentBubble = this.closest('.comment-bubble');
                        
                        allBubbles.forEach(bubble => {
                            if (bubble !== parentBubble) {
                                bubble.classList.add('chat-blur');
                            }
                        });

                        // Blur form komentar juga
                        const commentFormArea = document.querySelector('.border-top.pt-4');
                        if (commentFormArea && !this.closest('.border-top.pt-4')) {
                            commentFormArea.classList.add('chat-blur');
                        }
                    });
                    
                    dropdownToggle.addEventListener('hide.bs.dropdown', function() {
                        // Remove blur dari semua bubble
                        const allBubbles = document.querySelectorAll('.comment-bubble');
                        allBubbles.forEach(bubble => {
                            bubble.classList.remove('chat-blur');
                        });

                        // Remove blur dari form
                        const commentFormArea = document.querySelector('.border-top.pt-4');
                        if (commentFormArea) {
                            commentFormArea.classList.remove('chat-blur');
                        }
                    });
                }
            });
        });
    </script>
@endsection