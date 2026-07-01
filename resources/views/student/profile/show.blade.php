@extends('layouts.student')
@section('title', 'My Profile')

@section('content')
<style>
    .profile-card { border:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .profile-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; margin-bottom:2px; }
    .profile-value { font-size:.95rem; font-weight:500; color:#1e2a3a; margin-bottom:0; }
    .readonly-badge { font-size:.68rem; background:#f0f2f5; color:#6c757d; padding:2px 7px; border-radius:5px; vertical-align:middle; margin-left:6px; }
    .cgpa-pill { display:inline-block; padding:6px 16px; border-radius:20px; font-size:1.3rem; font-weight:700; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0" style="color:#1e2a3a;">My Profile</h5>
        <p style="font-size:.83rem;color:#6c757d;margin-top:2px;">Your academic and financial information.</p>
    </div>
</div>

<div class="row g-4">
    {{-- Identity card --}}
    <div class="col-md-5">
        <div class="card profile-card h-100">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3" style="width:72px;height:72px;background:linear-gradient(135deg,#14532d,#16a34a);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-person-fill" style="font-size:2rem;color:#fff;"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="color:#1e2a3a;">{{ $profile->name }}</h5>
                    <div style="font-family:monospace;color:#6c757d;font-size:.88rem;">{{ $profile->roll_number }}</div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="profile-label">Department</div>
                        <div class="profile-value">{{ $profile->department_name }}</div>
                    </div>
                    <div class="col-6">
                        <div class="profile-label">Gender</div>
                        <div class="profile-value">{{ ucfirst($profile->gender) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="profile-label">Semester</div>
                        <div class="profile-value">{{ $profile->semester }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Academic stats --}}
    <div class="col-md-7">
        <div class="card profile-card mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="font-size:.85rem;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">
                    <i class="bi bi-bar-chart me-2"></i>Academic Stats
                </h6>
                <div class="row g-3">
                    <div class="col-md-6 text-center">
                        <div class="profile-label">CGPA</div>
                        <div class="cgpa-pill mt-2
                            {{ $profile->cgpa >= 3.5 ? 'bg-success text-white' : ($profile->cgpa >= 3.0 ? 'bg-primary text-white' : 'bg-danger text-white') }}">
                            {{ number_format($profile->cgpa, 2) }}
                        </div>
                        <div style="font-size:.72rem;color:#6c757d;margin-top:4px;">out of 4.00</div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="profile-label">Family Income</div>
                        <div style="font-size:1.4rem;font-weight:700;color:#1e2a3a;margin-top:8px;">
                            ৳{{ number_format($profile->family_income) }}
                        </div>
                        <div style="font-size:.72rem;color:#6c757d;margin-top:4px;">annual</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
