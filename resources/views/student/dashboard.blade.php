@extends('layouts.student')
@section('title', 'My Dashboard')

@section('content')

{{-- Profile card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="card-title text-muted mb-3">My Profile</h6>
        @if($studentInfo && $studentInfo->student_id)
        <div class="row g-3">
            <div class="col-md-4">
                <small class="text-muted d-block">Full Name</small>
                <strong>{{ $studentInfo->user_name }}</strong>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">Roll Number</small>
                <strong class="font-monospace fs-5">{{ str_pad($studentInfo->roll_number, 7, '0', STR_PAD_LEFT) }}</strong>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">Department</small>
                <strong>{{ $studentInfo->department_name ?? '—' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Batch Year</small>
                <strong>20{{ substr(str_pad($studentInfo->roll_number, 7, '0', STR_PAD_LEFT), 0, 2) }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Dept Code</small>
                <strong>{{ $studentInfo->dept_code }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Class Roll</small>
                <strong>{{ substr(str_pad($studentInfo->roll_number, 7, '0', STR_PAD_LEFT), 4, 3) }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Semester</small>
                <strong>{{ $studentInfo->semester }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">CGPA</small>
                <strong>{{ $studentInfo->cgpa }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Gender</small>
                <strong>{{ ucfirst($studentInfo->gender) }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Family Income (Annual)</small>
                <strong>৳ {{ number_format($studentInfo->family_income) }}</strong>
            </div>
        </div>
        @else
        <p class="text-muted mb-0">Student profile not found.</p>
        @endif
    </div>
</div>

@endsection
