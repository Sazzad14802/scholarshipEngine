@extends('layouts.admin')
@section('title', 'Student Management')

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     PAGE STYLES
════════════════════════════════════════════════════════════════ --}}
<style>
    /* ── Statistics Cards ───────────────────────── */
    .stat-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
        transition: transform .18s, box-shadow .18s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,.12);
    }
    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .stat-label { font-size: .78rem; color: #6c757d; font-weight: 500; text-transform: uppercase; letter-spacing: .5px; }
    .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.1; }

    /* ── Filter Panel ───────────────────────────── */
    .filter-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }
    .filter-card .card-header {
        background: #1e2a3a;
        color: #fff;
        border-radius: 14px 14px 0 0;
        font-weight: 600;
        font-size: .9rem;
        padding: 12px 20px;
    }
    .filter-label {
        font-size: .78rem;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 4px;
    }
    .filter-control {
        font-size: .875rem;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 7px 11px;
    }
    .filter-control:focus {
        border-color: #4f6ef7;
        box-shadow: 0 0 0 3px rgba(79,110,247,.12);
    }

    /* ── Table ──────────────────────────────────── */
    .table-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        overflow: hidden;
    }
    .table-card .card-header {
        background: #fff;
        border-bottom: 2px solid #f0f2f5;
        padding: 14px 20px;
    }
    .students-table thead th {
        background: #f8f9fc;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        padding: 10px 14px;
        white-space: nowrap;
    }
    .students-table tbody tr {
        transition: background .12s;
    }
    .students-table tbody tr:hover {
        background: #f4f7ff;
    }
    .students-table tbody td {
        font-size: .875rem;
        padding: 10px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f2f5;
    }
    .sort-link {
        color: inherit;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .sort-link:hover { color: #4f6ef7; }
    .sort-icon { font-size: .7rem; opacity: .5; }
    .sort-icon.active { opacity: 1; color: #4f6ef7; }

    /* ── Status Badges ──────────────────────────── */
    .badge-awarded  { background: #d4edda; color: #155724; font-size: .72rem; }
    .badge-pending  { background: #fff3cd; color: #856404; font-size: .72rem; }
    .badge-rejected { background: #f8d7da; color: #721c24; font-size: .72rem; }
    .badge-none     { background: #e9ecef; color: #6c757d; font-size: .72rem; }

    /* ── CGPA Color Coding ──────────────────────── */
    .cgpa-high   { color: #198754; font-weight: 700; }
    .cgpa-mid    { color: #0d6efd; font-weight: 600; }
    .cgpa-low    { color: #dc3545; font-weight: 600; }

    /* ── Dept / Batch Stats Bars ────────────────── */
    .mini-stat-bar {
        height: 6px;
        border-radius: 3px;
        background: #e9ecef;
        overflow: hidden;
    }
    .mini-stat-bar-fill {
        height: 100%;
        border-radius: 3px;
        background: linear-gradient(90deg, #4f6ef7, #7c3aed);
    }

    /* ── Pagination ─────────────────────────────── */
    .pagination .page-link {
        border-radius: 8px !important;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        color: #4f6ef7;
        font-size: .85rem;
        padding: 6px 12px;
    }
    .pagination .page-item.active .page-link {
        background: #4f6ef7;
        border-color: #4f6ef7;
        color: #fff;
    }
    .pagination .page-link:hover {
        background: #f0f4ff;
    }

    /* ── Gender Badge ───────────────────────────── */
    .gender-m { background: #dbeafe; color: #1d4ed8; }
    .gender-f { background: #fce7f3; color: #9d174d; }

    /* ── Dept Badge ─────────────────────────────── */
    .dept-badge {
        font-size: .72rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        background: #ede9fe;
        color: #5b21b6;
    }
</style>



{{-- ═══════════════════════════════════════════════════════════
     FILTER PANEL
════════════════════════════════════════════════════════════════ --}}
<div class="card filter-card mb-4">
    <div class="card-header">
        <i class="bi bi-sliders me-2"></i>Filter &amp; Search Students
        @if(array_filter(array_diff_key($filters, ['minIncome'=>0,'maxIncome'=>0,'minCgpa'=>0,'maxCgpa'=>0,'perPage'=>0,'per_page'=>0,'direction'=>0,'sort'=>0])))
            <span class="badge bg-warning text-dark ms-2">Filters active</span>
        @endif
    </div>
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.students.index') }}" id="filterForm">

            <div class="row g-3 mb-3">
                {{-- Search --}}
                <div class="col-md-4">
                    <label class="filter-label" for="search">Search</label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:8px 0 0 8px;background:#f8f9fc;">
                            <i class="bi bi-search" style="font-size:.8rem;"></i>
                        </span>
                        <input type="text" id="search" name="search"
                               class="form-control filter-control"
                               style="border-radius: 0 8px 8px 0;"
                               placeholder="Roll number or student name…"
                               value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>

                {{-- Department --}}
                <div class="col-md-2">
                    <label class="filter-label" for="department">Department</label>
                    <select id="department" name="department" class="form-select filter-control">
                        <option value="">All Departments</option>
                        @foreach($deptOptions as $code => $abbr)
                            <option value="{{ $code }}" {{ ($filters['department'] ?? '') === $code ? 'selected' : '' }}>
                                {{ $abbr }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Batch --}}
                <div class="col-md-2">
                    <label class="filter-label" for="batch">Batch</label>
                    <select id="batch" name="batch" class="form-select filter-control">
                        <option value="">All Batches</option>
                        @foreach(['19','20','21','22'] as $b)
                            <option value="{{ $b }}" {{ ($filters['batch'] ?? '') === $b ? 'selected' : '' }}>
                                Batch {{ $b }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Gender --}}
                <div class="col-md-2">
                    <label class="filter-label" for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-select filter-control">
                        <option value="">All</option>
                        <option value="male"   {{ ($filters['gender'] ?? '') === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ ($filters['gender'] ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                {{-- Per page --}}
                <div class="col-md-2">
                    <label class="filter-label" for="per_page">Per Page</label>
                    <select id="per_page" name="per_page" class="form-select filter-control">
                        @foreach([10,20,50,100] as $n)
                            <option value="{{ $n }}" {{ $filters['perPage'] == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                {{-- CGPA Range --}}
                <div class="col-md-3">
                    <label class="filter-label">CGPA Range</label>
                    <div class="input-group">
                        <input type="number" name="min_cgpa" class="form-control filter-control"
                               placeholder="Min (0)" step="0.01" min="0" max="4"
                               value="{{ ($filters['minCgpa'] ?? 0) > 0 ? $filters['minCgpa'] : '' }}">
                        <span class="input-group-text">–</span>
                        <input type="number" name="max_cgpa" class="form-control filter-control"
                               placeholder="Max (4)" step="0.01" min="0" max="4"
                               value="{{ ($filters['maxCgpa'] ?? 4) < 4 ? $filters['maxCgpa'] : '' }}">
                    </div>
                </div>

                {{-- Income Range --}}
                <div class="col-md-4">
                    <label class="filter-label">Family Income Range (৳)</label>
                    <div class="input-group">
                        <input type="number" name="min_income" class="form-control filter-control"
                               placeholder="Min Income" step="100" min="0"
                               value="{{ ($filters['minIncome'] ?? 0) > 0 ? $filters['minIncome'] : '' }}">
                        <span class="input-group-text">–</span>
                        <input type="number" name="max_income" class="form-control filter-control"
                               placeholder="Max Income" step="100" min="0"
                               value="{{ ($filters['maxIncome'] ?? 999999999) < 999999999 ? $filters['maxIncome'] : '' }}">
                    </div>
                </div>

                {{-- Sort --}}
                <div class="col-md-3">
                    <label class="filter-label">Sort By</label>
                    <div class="input-group">
                        <select name="sort" class="form-select filter-control">
                            @foreach(['roll_number'=>'Roll Number','cgpa'=>'CGPA','family_income'=>'Family Income','batch'=>'Batch'] as $val => $label)
                                <option value="{{ $val }}" {{ $sortKey === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="direction" class="form-select filter-control">
                            <option value="asc"  {{ ($filters['direction'] ?? 'asc') === 'asc'  ? 'selected' : '' }}>↑ Asc</option>
                            <option value="desc" {{ ($filters['direction'] ?? 'asc') === 'desc' ? 'selected' : '' }}>↓ Desc</option>
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="border-radius:8px;">
                        <i class="bi bi-funnel me-1"></i>Apply
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary flex-fill" style="border-radius:8px;">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     STUDENT TABLE
════════════════════════════════════════════════════════════════ --}}
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-semibold" style="font-size:.95rem;">
                <i class="bi bi-table me-2 text-primary"></i>Students
            </span>
            <span class="badge bg-primary ms-2">{{ number_format($total) }} found</span>
        </div>
        <div class="text-muted" style="font-size:.78rem;">
            Showing {{ number_format($paginator->firstItem() ?? 0) }}–{{ number_format($paginator->lastItem() ?? 0) }}
            of {{ number_format($total) }}
        </div>
    </div>

    <div class="table-responsive">
        <table class="table students-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        @php
                            $rnDir = ($sortKey==='roll_number' && $filters['direction']==='asc') ? 'desc' : 'asc';
                        @endphp
                        <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort'=>'roll_number','direction'=>$rnDir])) }}"
                           class="sort-link">
                            Roll Number
                            <i class="bi {{ $sortKey==='roll_number' ? ($filters['direction']==='asc' ? 'bi-caret-up-fill active' : 'bi-caret-down-fill active') : 'bi-caret-up-fill' }} sort-icon"></i>
                        </a>
                    </th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>
                        @php
                            $batchDir = ($sortKey==='batch' && $filters['direction']==='asc') ? 'desc' : 'asc';
                        @endphp
                        <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort'=>'batch','direction'=>$batchDir])) }}"
                           class="sort-link">
                            Batch
                            <i class="bi {{ $sortKey==='batch' ? ($filters['direction']==='asc' ? 'bi-caret-up-fill active' : 'bi-caret-down-fill active') : 'bi-caret-up-fill' }} sort-icon"></i>
                        </a>
                    </th>
                    <th>Gender</th>
                    <th>
                        @php
                            $cgpaDir = ($sortKey==='cgpa' && $filters['direction']==='asc') ? 'desc' : 'asc';
                        @endphp
                        <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort'=>'cgpa','direction'=>$cgpaDir])) }}"
                           class="sort-link">
                            CGPA
                            <i class="bi {{ $sortKey==='cgpa' ? ($filters['direction']==='asc' ? 'bi-caret-up-fill active' : 'bi-caret-down-fill active') : 'bi-caret-up-fill' }} sort-icon"></i>
                        </a>
                    </th>
                    <th>
                        @php
                            $incomeDir = ($sortKey==='family_income' && $filters['direction']==='asc') ? 'desc' : 'asc';
                        @endphp
                        <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort'=>'family_income','direction'=>$incomeDir])) }}"
                           class="sort-link">
                            Family Income
                            <i class="bi {{ $sortKey==='family_income' ? ($filters['direction']==='asc' ? 'bi-caret-up-fill active' : 'bi-caret-down-fill active') : 'bi-caret-up-fill' }} sort-icon"></i>
                        </a>
                    </th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $i => $s)
                    @php
                        $rowNum = ($paginator->currentPage() - 1) * $paginator->perPage() + $i + 1;
                        $cgpaClass = $s->cgpa >= 3.5 ? 'cgpa-high' : ($s->cgpa >= 3.0 ? 'cgpa-mid' : 'cgpa-low');
                        $deptAbbr = match($s->dept_code) {
                            '03' => 'EEE',
                            '05' => 'ME',
                            '07' => 'CSE',
                            '09' => 'ECE',
                            default => 'BME',
                        };
                    @endphp
                    <tr>
                        <td style="color:#adb5bd;font-size:.8rem;">{{ $rowNum }}</td>
                        <td>
                            <span style="font-family:monospace;font-weight:600;color:#1e2a3a;font-size:.88rem;">
                                {{ $s->roll_number }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:500;color:#212529;">{{ $s->student_name }}</span>
                        </td>
                        <td>
                            <span class="dept-badge">{{ $deptAbbr }}</span>
                        </td>
                        <td>
                            <span class="badge" style="background:#e0e7ff;color:#3730a3;font-weight:600;font-size:.75rem;">
                                {{ $s->batch }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $s->gender === 'male' ? 'gender-m' : 'gender-f' }}"
                                  style="font-size:.75rem;font-weight:600;">
                                <i class="bi {{ $s->gender === 'male' ? 'bi-gender-male' : 'bi-gender-female' }} me-1"></i>
                                {{ ucfirst($s->gender) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $cgpaClass }}">{{ number_format($s->cgpa, 2) }}</span>
                        </td>
                        <td style="font-size:.85rem;color:#212529;">
                            ৳{{ number_format($s->family_income) }}
                        </td>
                        <td style="text-align:center;">
                            <span class="badge rounded-pill" style="background:#f0f2f5;color:#495057;font-weight:600;font-size:.75rem;">
                                {{ $s->semester }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div style="color:#adb5bd;">
                                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                No students found matching the current filters.
                            </div>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                Clear filters
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($paginator->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
            <div style="font-size:.82rem;color:#6c757d;">
                Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
                &nbsp;·&nbsp; {{ number_format($total) }} total records
            </div>
            <div>
                {{ $paginator->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@endsection
