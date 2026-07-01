@extends('layouts.admin')
@section('title', 'Scholarships')

@section('content')
<style>
    .card-table { border:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); overflow:hidden; }
    .card-table .card-header { background:#fff; border-bottom:2px solid #f0f2f5; padding:14px 20px; }
    table thead th { background:#f8f9fc; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#495057; border-bottom:2px solid #e9ecef; padding:10px 14px; white-space:nowrap; }
    table tbody tr:hover { background:#f4f7ff; }
    table tbody td { font-size:.875rem; padding:10px 14px; vertical-align:middle; border-bottom:1px solid #f0f2f5; }
    .filter-bar { background:#fff; border-radius:12px; padding:16px 20px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,.05); }
</style>

{{-- Filter bar --}}
<div class="filter-bar d-flex flex-wrap gap-3 align-items-end">
    <form method="GET" action="{{ route('admin.scholarships.index') }}" class="d-flex flex-wrap gap-2 align-items-end w-100">
        <div>
            <label class="form-label mb-1" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Department</label>
            <select name="department_id" class="form-select form-select-sm" style="min-width:160px;">
                <option value="">All</option>
                <option value="all_depts" {{ request('department_id')==='all_depts' ? 'selected' : '' }}>— No Department (Global)</option>
                @foreach($departments as $d)
                    <option value="{{ $d->department_id }}" {{ request('department_id')==$d->department_id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label mb-1" style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Application Required</label>
            <select name="application_required" class="form-select form-select-sm" style="min-width:140px;">
                <option value="">Both</option>
                <option value="1" {{ request('application_required')==='1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('application_required')==='0' ? 'selected' : '' }}>No (No App)</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-circle me-1"></i>Reset</a>
        </div>
        <div class="ms-auto">
            <a href="{{ route('admin.scholarships.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle me-1"></i>New Scholarship
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card card-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-award me-2 text-primary"></i>Scholarship Programs</span>
        <span class="badge bg-primary">{{ count($scholarships) }}</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Department</th>
                    <th style="text-align:center;">Recipients</th>
                    <th style="text-align:center;">App. Required</th>
                    <th style="text-align:center;">Applications</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scholarships as $s)
                    <tr>
                        <td><span class="fw-semibold">{{ $s->title }}</span></td>
                        <td style="color:#6c757d;font-size:.82rem;">{{ $s->department_name }}</td>
                        <td style="text-align:center;">{{ $s->recipient_count }}</td>
                        <td style="text-align:center;">
                            @if($s->application_required)
                                <span class="badge" style="background:#dbeafe;color:#1e40af;font-size:.72rem;">Yes</span>
                            @else
                                <span class="badge" style="background:#ede9fe;color:#5b21b6;font-size:.72rem;">No App</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span class="badge bg-secondary">{{ $s->total_applications }}</span>
                        </td>
                        <td style="text-align:center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.scholarships.show', $s->scholarship_id) }}"
                                   class="btn btn-outline-primary btn-sm" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.scholarships.edit', $s->scholarship_id) }}"
                                   class="btn btn-outline-secondary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        title="Delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $s->scholarship_id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Delete confirmation modal --}}
                    <div class="modal fade" id="deleteModal{{ $s->scholarship_id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title text-danger">Delete Scholarship</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to completely delete <strong>{{ $s->title }}</strong>?<br>
                                    <span class="text-danger small">This action cannot be undone and will also delete all associated applications!</span>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form method="POST" action="{{ route('admin.scholarships.destroy', $s->scholarship_id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete Scholarship</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:#adb5bd;">
                            <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            No scholarships found.
                            <a href="{{ route('admin.scholarships.create') }}" class="btn btn-sm btn-primary mt-2 d-block w-auto mx-auto">Create First Scholarship</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
