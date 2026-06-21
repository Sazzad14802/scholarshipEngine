@extends('layouts.student')
@section('title', 'My Dashboard')

@section('content')

{{-- Profile card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="card-title text-muted mb-3">My Profile</h6>
        @if($student)
        <div class="row g-3">
            <div class="col-md-4">
                <small class="text-muted d-block">Full Name</small>
                <strong>{{ $user->name }}</strong>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">Roll Number</small>
                <strong class="font-monospace fs-5">{{ $student->roll_number }}</strong>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">Department</small>
                <strong>{{ $student->department->name ?? '—' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Batch Year</small>
                <strong>20{{ $student->batch }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Dept Code</small>
                <strong>{{ $student->dept_code }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Class Roll</small>
                <strong>{{ $student->class_roll }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Semester</small>
                <strong>{{ $student->semester }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">CGPA</small>
                <strong>{{ $student->cgpa }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Gender</small>
                <strong>{{ ucfirst($student->gender) }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Family Income (Annual)</small>
                <strong>৳ {{ number_format($student->family_income) }}</strong>
            </div>
        </div>
        @else
        <p class="text-muted mb-0">Student profile not found.</p>
        @endif
    </div>
</div>

{{-- Stats row --}}
<div class="row g-3">
    <div class="col-sm-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-info bg-opacity-10 text-info fs-4">📄</div>
                <div>
                    <div class="text-muted small">My Applications</div>
                    <div class="fs-4 fw-bold">{{ $applicationCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 rounded bg-success bg-opacity-10 text-success fs-4">✅</div>
                <div>
                    <div class="text-muted small">Active Allocations</div>
                    <div class="fs-4 fw-bold">{{ $allocationCount }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
