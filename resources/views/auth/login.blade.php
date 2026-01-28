<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login LMS Diponegoro</title>
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #c7d2fe 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .login-card {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 380px;
            overflow: hidden;
        }
        .form-control {
            border-radius: 0.75rem;
            padding-left: 2.8rem;
            height: 50px;
            border: 1px solid #d1d5db;
            font-family: 'Courier New', monospace;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        .input-group-text {
            background: transparent;
            border: none;
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #9ca3af;
        }
        .btn-login {
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
        .logo-box {
            width: 70px;
            height: 70px;
            background: #2563eb;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }
        .form-label {
            font-weight: 600;
            color: #1f2937;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">

            <!-- Logo & Judul -->
            <div class="text-center mb-4">
                <div class="logo-box mx-auto mb-3">
                    <i class="bi bi-book fs-1 text-white"></i>
                </div>
                <h4 class="fw-bold text-dark">Login LMS Diponegoro</h4>
                <p class="text-muted">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Login Card -->
            <div class="card login-card mx-auto">
                <div class="card-body p-4">

                    <form method="POST" action="{{ route('login.process') }}">
                        @csrf

                        <!-- NIP / NISN -->
                        <div class="mb-3 position-relative">
                            <label for="nip" class="form-label">NIP / NIPD</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person-badge"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control @error('nip') is-invalid @enderror" 
                                    name="nip" 
                                    id="nip" 
                                    placeholder="Masukkan NIP atau NIPD" 
                                    value="{{ old('nip') }}" 
                                    required 
                                    autofocus>
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4 position-relative">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" 
                                    id="password" 
                                    placeholder="" 
                                    required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tombol Masuk -->
                        <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                            Masuk
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>