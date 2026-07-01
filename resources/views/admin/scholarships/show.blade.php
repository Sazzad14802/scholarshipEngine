@extends('layouts.admin')
@section('title', 'Scholarship — ' . $scholarship->title)

@section('content')
<style>
    .detail-card { border:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .detail-label { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; margin-bottom:2px; }
    .detail-value { font-size:.95rem; font-weight:500; color:#1e2a3a; }
    .badge-active   { background:#d1fae5; color:#065f46; }
    .badge-inactive { background:#e9ecef; color:#495057; }
    .badge-closed   { background:#fee2e2; color:#991b1b; }
    .stat-chip { background:#f8f9fc; border-radius:10px; padding:12px 18px; text-align:center; }
    .stat-chip .num { font-size:1.5rem; font-weight:700; color:#1e2a3a; }
    .stat-chip .lbl { font-size:.72rem; color:#6c757d; text-transform:uppercase; letter-spacing:.4px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.scholarships.edit', $scholarship->scholarship_id) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="bi bi-trash me-1"></i>Delete Scholarship
        </button>
    </div>
</div>

<div class="row g-4">
    {{-- Main info card --}}
    <div class="col-lg-8">
        <div class="card detail-card mb-4">
            <div class="card-body p-4">
                    <h4 class="fw-bold mb-0" style="color:#1e2a3a;">{{ $scholarship->title }}</h4>

                @if($scholarship->description)
                    <p style="color:#495057;font-size:.9rem;line-height:1.6;">{{ $scholarship->description }}</p>
                    <hr>
                @endif

                <div class="row g-3 mt-1">
                    <div class="col-sm-6">
                        <div class="detail-label">Department</div>
                        <div class="detail-value">{{ $scholarship->department_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Total Recipients</div>
                        <div class="detail-value">{{ $scholarship->recipient_count }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Application Required</div>
                        <div class="detail-value">
                            @if($scholarship->application_required)
                                <span class="badge" style="background:#dbeafe;color:#1e40af;">Yes — Students Apply</span>
                            @else
                                <span class="badge" style="background:#ede9fe;color:#5b21b6;">No — Auto Allocated</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Allow Existing Scholarship Holders</div>
                        <div class="detail-value">{{ $scholarship->allow_existing_scholarship ? 'Yes' : 'No' }}</div>
                    </div>
                    @if($scholarship->min_cgpa)
                    <div class="col-sm-6">
                        <div class="detail-label">Minimum CGPA</div>
                        <div class="detail-value">{{ number_format($scholarship->min_cgpa, 2) }}</div>
                    </div>
                    @endif
                    @if($scholarship->max_family_income)
                    <div class="col-sm-6">
                        <div class="detail-label">Max Family Income</div>
                        <div class="detail-value">৳{{ number_format($scholarship->max_family_income) }}</div>
                    </div>
                    @endif
                    @if($scholarship->gender_requirement)
                    <div class="col-sm-6">
                        <div class="detail-label">Gender Requirement</div>
                        <div class="detail-value">{{ ucfirst($scholarship->gender_requirement) }} only</div>
                    </div>
                    @endif
                    <div class="col-sm-6">
                        <div class="detail-label">Created By</div>
                        <div class="detail-value">{{ $scholarship->created_by_name }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Application stats sidebar --}}
    <div class="col-lg-4">
        <div class="card detail-card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="font-size:.85rem;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">
                    <i class="bi bi-bar-chart me-2"></i>Application Stats
                </h6>
                    <div class="stat-chip">
                        <div class="num">{{ number_format($scholarship->total_applications) }}</div>
                        <div class="lbl">Total Applications</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">Delete Scholarship</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to completely delete <strong>{{ $scholarship->title }}</strong>?<br>
                <span class="text-danger small">This action cannot be undone and will also delete all associated applications!</span>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.scholarships.destroy', $scholarship->scholarship_id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Scholarship</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
