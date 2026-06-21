@extends('layouts.admin')
@section('title', 'Create Scholarship')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">New Scholarship Program</h6>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.scholarships.store') }}">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3">
                        {{-- Recipient Count --}}
                        <div class="col-md-4">
                            <label for="recipient_count" class="form-label fw-semibold">No. of Recipients <span class="text-danger">*</span></label>
                            <input type="number" id="recipient_count" name="recipient_count" min="1"
                                   class="form-control @error('recipient_count') is-invalid @enderror"
                                   value="{{ old('recipient_count', 1) }}" required>
                            @error('recipient_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select id="status" name="status"
                                    class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active"   {{ old('status','active') === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="closed"   {{ old('status') === 'closed'   ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Department --}}
                        <div class="col-md-4">
                            <label for="department_id" class="form-label fw-semibold">Department</label>
                            <select id="department_id" name="department_id"
                                    class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->department_id }}"
                                        {{ old('department_id') == $dept->department_id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Min CGPA --}}
                        <div class="col-md-4">
                            <label for="min_cgpa" class="form-label fw-semibold">Minimum CGPA</label>
                            <input type="number" id="min_cgpa" name="min_cgpa"
                                   min="0" max="4" step="0.01"
                                   class="form-control @error('min_cgpa') is-invalid @enderror"
                                   value="{{ old('min_cgpa') }}" placeholder="Optional">
                            @error('min_cgpa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Max Family Income --}}
                        <div class="col-md-4">
                            <label for="max_family_income" class="form-label fw-semibold">Max Family Income</label>
                            <input type="number" id="max_family_income" name="max_family_income"
                                   min="0" step="1"
                                   class="form-control @error('max_family_income') is-invalid @enderror"
                                   value="{{ old('max_family_income') }}" placeholder="Optional (BDT)">
                            @error('max_family_income') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Gender Requirement --}}
                        <div class="col-md-4">
                            <label for="gender_requirement" class="form-label fw-semibold">Gender Requirement</label>
                            <select id="gender_requirement" name="gender_requirement"
                                    class="form-select @error('gender_requirement') is-invalid @enderror">
                                <option value=""     {{ old('gender_requirement') === null ? 'selected' : '' }}>Any</option>
                                <option value="male" {{ old('gender_requirement') === 'male'   ? 'selected' : '' }}>Male Only</option>
                                <option value="female" {{ old('gender_requirement') === 'female' ? 'selected' : '' }}>Female Only</option>
                            </select>
                            @error('gender_requirement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Application Required --}}
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold d-block">Application Required? <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="application_required"
                                   id="app_req_yes" value="1"
                                   {{ old('application_required', '1') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="app_req_yes">Yes — Students must apply</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="application_required"
                                   id="app_req_no" value="0"
                                   {{ old('application_required') === '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="app_req_no">No — Auto-allocate</label>
                        </div>
                        @error('application_required') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    {{-- Allow Existing Scholarship Holders --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">Allow Existing Scholarship Holders? <span class="text-danger">*</span></label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="allow_existing_scholarship"
                                   id="allow_yes" value="1"
                                   {{ old('allow_existing_scholarship', '1') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_yes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="allow_existing_scholarship"
                                   id="allow_no" value="0"
                                   {{ old('allow_existing_scholarship') === '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_no">No — Reject existing holders</label>
                        </div>
                        @error('allow_existing_scholarship') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Save Scholarship
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
