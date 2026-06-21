@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-primary bg-opacity-10 text-primary fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Students</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_students'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-warning bg-opacity-10 text-warning fs-4">
                    <i class="bi bi-award-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Scholarships</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_scholarships'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-info bg-opacity-10 text-info fs-4">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div>
                    <div class="text-muted small">Applications</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_applications'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-success bg-opacity-10 text-success fs-4">
                    <i class="bi bi-check2-all"></i>
                </div>
                <div>
                    <div class="text-muted small">Allocations</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_allocations'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body d-flex justify-content-between align-items-center">
        <span class="text-muted small">Quick Actions</span>
        <a href="{{ route('admin.scholarships.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Scholarship
        </a>
    </div>
</div>
@endsection
