<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Scholarship Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem 0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        .card-header { background: #1a3a6b; color: #fff; border-radius: 12px 12px 0 0 !important; padding: 1.5rem; text-align: center; }
        .btn-primary { background: #1a3a6b; border-color: #1a3a6b; }
        .btn-primary:hover { background: #15306b; }
        .hint { font-size: .78rem; color: #6c757d; margin-top: .2rem; }
        #student-fields { display: none; }
    </style>
</head>
<body>
<div class="card" style="width: 480px;">
    <div class="card-header">
        <h5 class="mb-0">🎓 Create Account</h5>
    </div>
    <div class="card-body p-4">

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Role --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Account Type</label>
                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
                    <option value="STUDENT" {{ old('role','STUDENT')==='STUDENT'?'selected':'' }}>Student</option>
                    <option value="ADMIN"   {{ old('role')==='ADMIN'?'selected':'' }}>Admin</option>
                </select>
            </div>

            {{-- Full Name --}}
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Full Name</label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- ── Student-only fields ───────────────────────── --}}
            <div id="student-fields">
                <div class="mb-3">
                    <label for="roll_number" class="form-label fw-semibold">Roll Number</label>
                    <input type="text" id="roll_number" name="roll_number"
                           class="form-control @error('roll_number') is-invalid @enderror"
                           value="{{ old('roll_number') }}"
                           placeholder="e.g. 2207026"
                           maxlength="7">
                    <div class="hint">Format: <strong>BB DD RRR</strong> — Batch (22) + Dept code (07) + Class roll (026)</div>
                    @error('roll_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cgpa" class="form-label fw-semibold">CGPA</label>
                        <input type="number" id="cgpa" name="cgpa" step="0.01" min="0" max="4"
                               class="form-control @error('cgpa') is-invalid @enderror"
                               value="{{ old('cgpa') }}" placeholder="0.00 – 4.00">
                        @error('cgpa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="family_income" class="form-label fw-semibold">Family Income (৳/yr)</label>
                        <input type="number" id="family_income" name="family_income" min="0"
                               class="form-control @error('family_income') is-invalid @enderror"
                               value="{{ old('family_income') }}" placeholder="Annual income">
                        @error('family_income')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label fw-semibold">Gender</label>
                    <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">— Select —</option>
                        <option value="male"   {{ old('gender')==='male'?'selected':'' }}>Male</option>
                        <option value="female" {{ old('gender')==='female'?'selected':'' }}>Female</option>
                        <option value="other"  {{ old('gender')==='other'?'selected':'' }}>Other</option>
                    </select>
                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- ── Admin-only field ─────────────────────────── --}}
            <div id="admin-fields" style="display:none;">
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">Username</label>
                    <input type="text" id="username" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" placeholder="e.g. admin2">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       value="123" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control" value="123" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <hr class="my-3">
        <p class="text-center mb-0 small text-muted">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</div>

<script>
    const roleSelect    = document.getElementById('role');
    const studentFields = document.getElementById('student-fields');
    const adminFields   = document.getElementById('admin-fields');
    const rollInput     = document.getElementById('roll_number');
    const genderSel     = document.getElementById('gender');
    const cgpaInput     = document.getElementById('cgpa');
    const incomeInput   = document.getElementById('family_income');

    function toggleFields() {
        const isStudent = roleSelect.value === 'STUDENT';
        studentFields.style.display = isStudent ? 'block' : 'none';
        adminFields.style.display   = isStudent ? 'none'  : 'block';

        // Required attributes toggled with role
        rollInput.required    = isStudent;
        genderSel.required    = isStudent;
        cgpaInput.required    = isStudent;
        incomeInput.required  = isStudent;
    }

    roleSelect.addEventListener('change', toggleFields);
    toggleFields(); // run on page load
</script>
</body>
</html>
