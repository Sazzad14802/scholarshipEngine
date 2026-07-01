@extends('layouts.admin')
@section('title', 'Create Scholarship')

@section('content')
<style>
    .form-card { border:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .section-label { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; border-bottom:1px solid #f0f2f5; padding-bottom:8px; margin-bottom:16px; }
</style>

<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
    <h5 class="mb-0 fw-semibold" style="color:#1e2a3a;">New Scholarship Program</h5>
</div>

<div class="card form-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.scholarships.store') }}">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Basic info --}}
            <p class="section-label"><i class="bi bi-info-circle me-2"></i>Basic Information</p>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments (Global)</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}"
                                {{ old('department_id') == $dept->department_id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Number of Recipients <span class="text-danger">*</span></label>
                    <input type="number" name="slots" class="form-control @error('slots') is-invalid @enderror"
                           value="{{ old('slots', 1) }}" min="1" required>
                    @error('slots')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Amount (per recipient) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount', 0) }}" min="0" required>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Deadline <span class="text-danger">*</span></label>
                    <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                           value="{{ old('deadline') }}" required>
                    @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Application settings --}}
            <p class="section-label"><i class="bi bi-clipboard-check me-2"></i>Application Settings</p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Application Required <span class="text-danger">*</span></label>
                    <select name="application_required" class="form-select" required>
                        <option value="1" {{ old('application_required', 1) == 1 ? 'selected' : '' }}>Yes — Students Apply</option>
                        <option value="0" {{ old('application_required') == '0' ? 'selected' : '' }}>No — No Application Required</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Allow Existing Scholarship Holders <span class="text-danger">*</span></label>
                    <select name="allow_existing_scholarship" class="form-select" required>
                        <option value="0" {{ old('allow_existing_scholarship') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('allow_existing_scholarship', 1) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>

            {{-- Eligibility criteria --}}
            <p class="section-label"><i class="bi bi-sliders me-2"></i>Eligibility Criteria (optional)</p>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Minimum CGPA</label>
                    <input type="number" name="min_cgpa" class="form-control"
                           value="{{ old('min_cgpa') }}" step="0.01" min="0" max="4" placeholder="e.g. 3.00">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Max Family Income (৳)</label>
                    <input type="number" name="max_income" class="form-control"
                           value="{{ old('max_income') }}" min="0" placeholder="e.g. 300000">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Gender Requirement</label>
                    <select name="gender_requirement" class="form-select">
                        <option value="">Any Gender</option>
                        <option value="male"   {{ old('gender_requirement') === 'male'   ? 'selected' : '' }}>Male Only</option>
                        <option value="female" {{ old('gender_requirement') === 'female' ? 'selected' : '' }}>Female Only</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i>Save Scholarship
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
