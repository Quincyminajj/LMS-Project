<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LMS')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
        }

        main.content-area {
            all: revert;
        }
        
        main.content-area * {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="@yield('body-class', 'bg-light')">
    <!-- Responsive Header -->
    <header class="bg-blue-600 text-white shadow-lg" style="background-color: #2563eb !important;">
        <div class="container mx-auto px-3 sm:px-6 py-3 sm:py-4">
            <div class="flex items-center justify-between">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <i class="fas fa-book text-lg sm:text-2xl"></i>
                    <div>
                        <h1 class="text-base sm:text-xl font-bold">@yield('header-title', 'LMS Diponegoro')</h1>
                        <p class="text-xs sm:text-sm text-blue-100">@yield('header-subtitle', 'Portal Pembelajaran')</p>
                    </div>
                </div>
                
                <!-- User Info & Logout -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <div class="text-right max-w-[140px] sm:max-w-none">
                        <p class="font-semibold text-xs sm:text-sm break-words leading-tight">{{ session('user_name') }}</p>
                        <p class="text-xs text-blue-100 whitespace-nowrap">
                            @if(session('user_role') == 'guru')
                                NIP: {{ session('identifier') }}
                            @elseif(session('user_role') == 'siswa')
                                NISN: {{ session('identifier') }}
                            @else
                                {{ session('identifier') }}
                            @endif
                        </p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-full flex items-center justify-center text-sm sm:text-lg font-bold flex-shrink-0">
                        {{ substr(session('user_name'), 0, 1) }}
                    </div>
                    <a href="#" class="text-white hover:text-blue-200 flex-shrink-0" id="logoutButton">
                        <i class="fas fa-sign-out-alt text-lg sm:text-xl"></i>
                    </a>
                    <form id="logoutForm" action="{{ route('logout') }}" method="GET" class="d-none"></form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="@yield('main-class', 'content-area')">
        @hasSection('use-container')
            <div class="container my-3 my-md-4 px-3 px-md-4">
                @yield('content')
            </div>
        @else
            @yield('content')
        @endif
    </main>

    @yield('modal')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Session Messages -->
    <script>
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        @endif
        
        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    </script>

    <!-- Logout Confirmation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logoutButton');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: "Yakin ingin logout?",
                        text: "Anda akan keluar dari sistem.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, Logout",
                        cancelButtonText: "Batal",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('logoutForm').submit();
                        }
                    });
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>