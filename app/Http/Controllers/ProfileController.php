<?php
// File: app/Http/Controllers/ProfileController.php
//
// Student profile: view and edit own STUDENT record.
// Editable fields: cgpa, family_income, semester
// Read-only: name (USERS.name), username (roll number), gender, department
//
// UPDATE uses raw DB::statement() — no Eloquent save().
// Oracle note: DB::select() returns lowercase column names via PDO OCI8.

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    // ── SHOW ──────────────────────────────────────────────────────────
    public function show()
    {
        $user    = Auth::user();
        $profile = DB::select("
            SELECT
                u.name,
                u.username       AS roll_number,
                d.name           AS department_name,
                s.gender,
                s.cgpa,
                s.family_income,
                s.semester,
                s.student_id
            FROM   STUDENT s
            JOIN   USERS      u ON u.user_id       = s.user_id
            JOIN   DEPARTMENT d ON d.department_id = s.department_id
            WHERE  s.user_id = ?
        ", [$user->user_id]);

        if (empty($profile)) {
            abort(403, 'Student record not found.');
        }

        $profile = $profile[0];
        return view('student.profile.show', compact('profile'));
    }
}
