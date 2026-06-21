<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ScholarshipProgram;
use App\Models\Application;
use App\Models\Allocation;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students'      => Student::count(),
            'total_scholarships'  => ScholarshipProgram::count(),
            'total_applications'  => Application::count(),
            'total_allocations'   => Allocation::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
