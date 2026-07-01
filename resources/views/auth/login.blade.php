<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Scholarship Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        .card-header { background: #1a3a6b; color: #fff; border-radius: 12px 12px 0 0 !important; padding: 1.5rem; text-align: center; }
        .btn-primary { background: #1a3a6b; border-color: #1a3a6b; }
        .btn-primary:hover { background: #15306b; border-color: #122d65; }
        .hint { font-size: .8rem; color: #6c757d; margin-top: .25rem; }
    </style>
</head>
<body>
<div class="card" style="width: 420px;">
    <div class="card-header">
        <h5 class="mb-1">🎓 Scholarship Allocation Engine</h5>
    </div>
    <div class="card-body p-4">



        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- User ID --}}
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">User ID</label>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username') }}"
                       placeholder="Enter your User ID"
                       autofocus
                       autocomplete="username">
                <!-- <div class="hint">Admin: <strong>admin</strong> &nbsp;|&nbsp; Student: roll number e.g. <strong>2207026</strong></div> -->
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Enter your password"
                       autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>
    </div>
</div>
</body>
</html>
