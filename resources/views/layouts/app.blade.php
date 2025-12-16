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
</head>
<body class="bg-light">
    <!-- Responsive Header dengan Tailwind -->
    <header class="bg-blue-600 text-white shadow-lg" style="background-color: #2563eb !important;">
        <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex items-center justify-between">
                <!-- Logo & Title - Responsive -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <i class="fas fa-book text-xl sm:text-2xl"></i>
                    <div>
                        <span class="d-none d-sm-inline">LMS Diponegoro</span>
                        <span class="d-inline d-sm-none">LMS</span>
                        <p class="text-xs sm:text-sm text-blue-100">Portal Pembelajaran</p>
                    </div>
                </div>

                <!-- User Info & Actions - Responsive -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- User Info - Hide details on mobile, show on tablet+ -->
                    <div class="hidden md:block text-right">
                        @auth
                            <p class="font-semibold text-sm">{{ session('user_name', Auth::user()->name ?? 'User') }}</p>
                            <p class="text-xs text-blue-100">
                                @if(session('user_role') == 'guru')
                                    NIP: {{ session('identifier', '-') }}
                                @elseif(session('user_role') == 'siswa')
                                    NIS: {{ session('identifier', '-') }}
                                @else
                                    {{ ucfirst(session('user_role', 'User')) }}
                                @endif
                            </p>
                        @endauth
                    </div>

                    <!-- Logout Button -->
                    <button type="button" 
                            class="text-white hover:text-blue-200 transition p-2" 
                            id="logoutButton"
                            title="Logout">
                        <i class="fas fa-sign-out-alt text-lg sm:text-xl"></i>
                    </button>
                    
                    <form id="logoutForm" action="{{ route('logout') }}" method="GET" class="hidden"></form>
                </div>
            </div>

            <!-- Mobile User Info - Show only on mobile -->
                @auth
                    <p class="text-sm font-semibold">{{ session('user_name', Auth::user()->name ?? 'User') }}</p>
                    <p class="text-xs text-blue-100">
                        @if(session('user_role') == 'guru')
                            NIP: {{ session('identifier', '-') }}
                        @elseif(session('user_role') == 'siswa')
                            NIS: {{ session('identifier', '-') }}
                        @else
                            {{ ucfirst(session('user_role', 'User')) }}
                        @endif
                    </p>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content dengan Bootstrap (agar tidak rusak) -->
    <main class="content-area">
        <div class="container my-3 my-md-4 px-3 px-md-4">
            @yield('content')
        </div>
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

        // Logout Confirmation
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
    </script>
    
    <!-- Custom Scripts -->
    @yield('scripts')

</body>
</html>