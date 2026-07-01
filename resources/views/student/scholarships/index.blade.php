@extends('layouts.student')
@section('title', 'Browse Scholarships')

@section('content')
<style>
    .schol-card { border:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); transition:transform .15s, box-shadow .15s; }
    .schol-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.1); }
    .filter-bar { background:#fff; border-radius:12px; padding:16px 20px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,.05); }
    .badge-yes   { background:#dbeafe; color:#1e40af; }
    .badge-auto  { background:#ede9fe; color:#5b21b6; }
    .criteria-tag { font-size:.72rem; background:#f8f9fc; border-radius:6px; padding:3px 8px; color:#495057; display:inline-block; margin:2px; }
</style>

{{-- Info note --}}
<div class="alert alert-info d-flex align-items-center py-2 mb-4" style="border-radius:10px;font-size:.85rem;">
    <i class="bi bi-info-circle-fill me-2 text-primary"></i>
    Only scholarships you are fully eligible for (based on department, CGPA, income, and gender) are shown here. Scholarships marked <strong class="mx-1">No App</strong> require no application.
</div>

{{-- Card grid --}}
<div class="row g-3">
    @forelse($scholarships as $s)
        @php $alreadyApplied = in_array($s->scholarship_id, $appliedSet); @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card schol-card h-100">
                <div class="card-body p-4 d-flex flex-column">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0" style="color:#1e2a3a;font-size:.95rem;">{{ $s->title }}</h6>
                        @if($s->application_required)
                            <span class="badge badge-yes ms-2 flex-shrink-0" style="font-size:.7rem;">Apply</span>
                        @else
                            <span class="badge badge-auto ms-2 flex-shrink-0" style="font-size:.7rem;">No App</span>
                        @endif
                    </div>

                    {{-- Department --}}
                    <div style="font-size:.8rem;color:#6c757d;margin-bottom:10px;">
                        <i class="bi bi-building me-1"></i>{{ $s->department_name }}
                    </div>

                    {{-- Description excerpt --}}
                    @if($s->description)
                        <p style="font-size:.82rem;color:#495057;line-height:1.5;" class="mb-3">
                            {{ Str::limit($s->description, 100) }}
                        </p>
                    @endif

                    {{-- Criteria chips --}}
                    <div class="mb-3">
                        @if($s->min_cgpa)
                            <span class="criteria-tag"><i class="bi bi-graph-up me-1"></i>Min CGPA {{ number_format($s->min_cgpa, 2) }}</span>
                        @endif
                        @if($s->max_family_income)
                            <span class="criteria-tag"><i class="bi bi-currency-exchange me-1"></i>Income ≤ ৳{{ number_format($s->max_family_income) }}</span>
                        @endif
                        @if($s->gender_requirement)
                            <span class="criteria-tag"><i class="bi bi-gender-ambiguous me-1"></i>{{ ucfirst($s->gender_requirement) }} only</span>
                        @endif
                        <span class="criteria-tag"><i class="bi bi-people me-1"></i>{{ $s->recipient_count }} recipients</span>
                    </div>

                    {{-- Action button --}}
                    <div class="mt-auto">
                        @if($s->application_required)
                            @if($alreadyApplied)
                                <button class="btn btn-outline-success btn-sm w-100" disabled>
                                    <i class="bi bi-check-circle me-1"></i>Applied
                                </button>
                            @else
                                <form method="POST" action="{{ route('student.scholarships.apply', $s->scholarship_id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm w-100"
                                            onclick="return confirm('Apply to {{ addslashes($s->title) }}?')">
                                        <i class="bi bi-send me-1"></i>Apply Now
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="text-center" style="font-size:.8rem;color:#7c3aed;font-weight:500;">
                                <i class="bi bi-lightning-fill me-1"></i>No Application Required
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5" style="color:#adb5bd;">
                <i class="bi bi-search" style="font-size:2.5rem;display:block;margin-bottom:12px;"></i>
                No scholarships match your profile right now.
            </div>
        </div>
    @endforelse
</div>
@endsection
