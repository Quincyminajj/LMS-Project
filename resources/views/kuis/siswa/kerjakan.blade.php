@extends('layouts.app')

@section('title', 'Mengerjakan Kuis - ' . $kuis->judul)

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Timer & Info Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h6 class="mb-0 fw-semibold">{{ $kuis->judul }}</h6>
                            <small class="text-muted">{{ $soal->count() }} Soal</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock-fill text-danger me-2 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Sisa Waktu</small>
                                    <strong class="fs-5" id="timer">{{ $kuis->durasi }}:00</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-success" onclick="confirmSubmit()">
                                <i class="bi bi-check-circle"></i> Submit Jawaban
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('kuis.siswa.submit', $kuis->id) }}" method="POST" id="formKuis">
                @csrf
                
                <!-- Soal -->
                @foreach($soal as $index => $item)
                    <div class="card border-0 shadow-sm mb-4" id="soal-{{ $index + 1 }}">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="fw-semibold mb-0">
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    Soal {{ $index + 1 }}
                                </h5>
                                <span class="badge bg-light text-dark">Pilihan Ganda</span>
                            </div>
                            
                            <div class="mb-4">
                                <p class="fs-6 mb-0" style="line-height: 1.8;">{{ $item->pertanyaan }}</p>
                            </div>
                            
                            <div class="options-container">
                                <!-- Opsi A -->
                                <div class="form-check mb-3 p-3 border rounded option-item">
                                    <input class="form-check-input" type="radio" 
                                           name="jawaban[{{ $item->id }}]" 
                                           id="jawaban_{{ $item->id }}_A" 
                                           value="A"
                                           required>
                                    <label class="form-check-label w-100 cursor-pointer" for="jawaban_{{ $item->id }}_A">
                                        <strong>A.</strong> {{ $item->opsi_a }}
                                    </label>
                                </div>
                                
                                <!-- Opsi B -->
                                <div class="form-check mb-3 p-3 border rounded option-item">
                                    <input class="form-check-input" type="radio" 
                                           name="jawaban[{{ $item->id }}]" 
                                           id="jawaban_{{ $item->id }}_B" 
                                           value="B">
                                    <label class="form-check-label w-100 cursor-pointer" for="jawaban_{{ $item->id }}_B">
                                        <strong>B.</strong> {{ $item->opsi_b }}
                                    </label>
                                </div>
                                
                                <!-- Opsi C -->
                                <div class="form-check mb-3 p-3 border rounded option-item">
                                    <input class="form-check-input" type="radio" 
                                           name="jawaban[{{ $item->id }}]" 
                                           id="jawaban_{{ $item->id }}_C" 
                                           value="C">
                                    <label class="form-check-label w-100 cursor-pointer" for="jawaban_{{ $item->id }}_C">
                                        <strong>C.</strong> {{ $item->opsi_c }}
                                    </label>
                                </div>
                                
                                <!-- Opsi D -->
                                <div class="form-check mb-0 p-3 border rounded option-item">
                                    <input class="form-check-input" type="radio" 
                                           name="jawaban[{{ $item->id }}]" 
                                           id="jawaban_{{ $item->id }}_D" 
                                           value="D">
                                    <label class="form-check-label w-100 cursor-pointer" for="jawaban_{{ $item->id }}_D">
                                        <strong>D.</strong> {{ $item->opsi_d }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                            
                            <button type="button" class="btn btn-success" onclick="confirmSubmit()">
                                <i class="bi bi-send"></i> Submit Jawaban
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .option-item {
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .option-item:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd !important;
    }
    
    .option-item:has(input:checked) {
        background-color: #e7f3ff;
        border-color: #0d6efd !important;
        border-width: 2px !important;
    }
    
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .soal-nav-btn {
        min-width: 40px;
    }
    
    .soal-nav-btn.answered {
        background-color: #198754;
        color: white;
        border-color: #198754;
    }
    
    .sticky-top {
        position: sticky;
        top: 70px;
        z-index: 100;
    }
    
    .sticky-bottom {
        position: sticky;
        bottom: 0;
        z-index: 100;
    }
</style>
@endsection

@section('scripts')
<script>
    // Timer countdown - DIPERBAIKI
    const durasiMenit = {{ $kuis->durasi }};
    
    // Parse waktu mulai dengan format ISO
    const mulaiPadaStr = '{{ \Carbon\Carbon::parse($attempt->mulai_pada)->toIso8601String() }}';
    const mulaiPada = new Date(mulaiPadaStr).getTime();
    const selesaiPada = mulaiPada + (durasiMenit * 60 * 1000);
    
    const timerElement = document.getElementById('timer');
    
    const countdown = setInterval(function() {
        const now = new Date().getTime();
        const distance = selesaiPada - now;
        
        if (distance < 0) {
            clearInterval(countdown);
            timerElement.innerHTML = "WAKTU HABIS";
            timerElement.classList.add('text-danger');
            
            // Auto submit
            Swal.fire({
                title: 'Waktu Habis!',
                text: 'Jawaban Anda akan otomatis disubmit.',
                icon: 'warning',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                document.getElementById('formKuis').submit();
            });
            
            return;
        }
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        timerElement.innerHTML = minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
        
        // Warning jika waktu < 5 menit
        if (minutes < 5) {
            timerElement.classList.add('text-danger', 'fw-bold');
        }
        
        // Warning jika waktu < 1 menit
        if (minutes < 1) {
            timerElement.parentElement.parentElement.classList.add('pulse-warning');
        }
    }, 1000);
    
    // Update status tombol navigasi saat jawaban dipilih
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateNavButtons();
        });
    });
    
    function updateNavButtons() {
        const totalSoal = {{ $soal->count() }};
        
        for (let i = 1; i <= totalSoal; i++) {
            const soalRadios = document.querySelectorAll(`input[name*="jawaban"]:checked`);
            const navBtn = document.querySelector(`.soal-nav-btn[data-soal="${i}"]`);
            
            // Cek apakah soal ini sudah dijawab
            const answered = Array.from(soalRadios).some(radio => {
                return radio.closest('.card').id === `soal-${i}`;
            });
            
            if (answered) {
                navBtn.classList.add('answered');
            } else {
                navBtn.classList.remove('answered');
            }
        }
    }
    
    function scrollToSoal(number) {
        const element = document.getElementById(`soal-${number}`);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    function confirmSubmit() {
        const totalSoal = {{ $soal->count() }};
        const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
        
        if (answeredCount < totalSoal) {
            Swal.fire({
                title: 'Peringatan!',
                html: `Anda baru menjawab <strong>${answeredCount}</strong> dari <strong>${totalSoal}</strong> soal.<br>Yakin ingin submit?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Submit!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
        } else {
            Swal.fire({
                title: 'Submit Jawaban?',
                text: 'Anda tidak dapat mengubah jawaban setelah submit.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Submit!',
                cancelButtonText: 'Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
        }
    }
    
    function submitForm() {
        // Nonaktifkan beforeunload sebelum submit
        window.removeEventListener('beforeunload', preventLeave);
        
        Swal.fire({
            title: 'Menyimpan Jawaban...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        document.getElementById('formKuis').submit();
    }
    
    // Prevent accidental page leave
    function preventLeave(e) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
    
    window.addEventListener('beforeunload', preventLeave);
    
    // Initialize nav buttons on load
    updateNavButtons();
</script>

<style>
    @keyframes pulse-warning {
        0%, 100% { background-color: transparent; }
        50% { background-color: #fff3cd; }
    }
    
    .pulse-warning {
        animation: pulse-warning 1s infinite;
    }
</style>
@endsection