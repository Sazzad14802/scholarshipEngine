<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $student = $user->student()->with('department')->first();

        $applicationCount = $student
            ? $student->applications()->count()
            : 0;

        $allocationCount = $student
            ? $student->allocations()->where('status', 'active')->count()
            : 0;

        return view('student.dashboard', compact('user', 'student', 'applicationCount', 'allocationCount'));
    }
}
