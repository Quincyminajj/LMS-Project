<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 22rem;">
        <h4 class="text-center mb-3">Login LMS Diponegoro</h4>
        <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="mb-3">
                <label for="nip" class="form-label">NIP / NISN</label>
                <input type="text" class="form-control" name="nip" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
</body>
</html>
