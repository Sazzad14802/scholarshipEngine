<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Fetch user + student + department info in one query
        $studentInfo = DB::selectOne("
            SELECT u.name AS user_name, u.username AS user_email,
                   s.student_id, s.roll_number, s.cgpa, s.semester, s.gender, s.family_income,
                   d.name AS department_name, d.dept_code
            FROM USERS u
            LEFT JOIN STUDENT s ON u.user_id = s.user_id
            LEFT JOIN DEPARTMENT d ON s.department_id = d.department_id
            WHERE u.user_id = ?
        ", [$userId]);

        return view('student.dashboard', compact('studentInfo'));
    }
}
