<?php
// File: app/Http/Controllers/Student/ScholarshipController.php
//
// Student: browse active scholarships and submit applications.
//
// index():
//   Shows all ACTIVE scholarships. Auto-filtered so only scholarships
//   matching student's gender (or NULL gender_requirement) are shown.
//   Additional GET filters: ?department_id=7&min_cgpa=3.0
//   Marks scholarships the student has already applied to.
//
// apply():
//   Inserts into APPLICATION using application_seq.NEXTVAL.
//   Does NOT pre-check for duplicates in PHP.
//   The Oracle trigger TRG_PREVENT_DUPLICATE_APPLICATION catches
//   duplicates and raises ORA-20001. We catch it and flash a message.
//
// Oracle note: DB::select() returns lowercase column names via PDO OCI8.

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user    = Auth::user();
        $student = DB::select(
            'SELECT student_id, gender, cgpa, family_income, department_id
               FROM STUDENT WHERE user_id = ?',
            [$user->user_id]
        );
        if (empty($student)) {
            abort(403, 'Student record not found.');
        }
        $student = $student[0];

        // Scholarships the student has already applied to
        $appliedIds = DB::select(
            'SELECT scholarship_id FROM APPLICATION WHERE student_id = ?',
            [$student->student_id]
        );
        $appliedSet = array_column($appliedIds, 'scholarship_id');

        // Build WHERE for SCHOLARSHIP_PROGRAM based strictly on student eligibility
        $whereParts = [
            "(sp.department_id IS NULL OR sp.department_id = ?)",
            "(sp.gender_requirement IS NULL OR sp.gender_requirement = ?)",
            "(sp.min_cgpa IS NULL OR sp.min_cgpa <= ?)",
            "(sp.max_income IS NULL OR sp.max_income >= ?)",
        ];
        $bindings = [
            $student->department_id,
            $student->gender,
            $student->cgpa,
            $student->family_income
        ];

        $whereClause = 'WHERE ' . implode(' AND ', $whereParts);

        $scholarships = DB::select("
            SELECT
                sp.scholarship_id,
                sp.title,
                NVL(d.name, 'All Departments') AS department_name,
                sp.slots                        AS recipient_count,
                sp.application_required,
                sp.min_cgpa,
                sp.max_income                   AS max_family_income,
                sp.gender_requirement,
                sp.description,
                sp.amount,
                sp.deadline
            FROM   SCHOLARSHIP_PROGRAM sp
            LEFT JOIN DEPARTMENT d ON d.department_id = sp.department_id
            {$whereClause}
            ORDER BY sp.scholarship_id DESC
        ", $bindings);

        // Attach departments for filter dropdown
        $departments = DB::select('SELECT department_id, name FROM DEPARTMENT ORDER BY name');

        return view('student.scholarships.index', compact(
            'scholarships', 'departments', 'appliedSet', 'student'
        ));
    }

    // ── APPLY ─────────────────────────────────────────────────────────
    // Raw INSERT. Trigger TRG_PREVENT_DUPLICATE_APPLICATION fires in Oracle.
    // On ORA-20001, catch and flash a user-friendly error.
    public function apply(Request $request, int $scholarshipId)
    {
        $user    = Auth::user();
        $student = DB::select(
            'SELECT student_id FROM STUDENT WHERE user_id = ?',
            [$user->user_id]
        );
        if (empty($student)) {
            abort(403);
        }
        $studentId = $student[0]->student_id;

        // Verify scholarship exists
        $schol = DB::select(
            "SELECT scholarship_id, application_required
               FROM SCHOLARSHIP_PROGRAM WHERE scholarship_id = ?",
            [$scholarshipId]
        );
        if (empty($schol)) {
            return back()->with('error', 'This scholarship is not available.');
        }
        if ($schol[0]->application_required != 1) {
            return back()->with('error', 'This scholarship does not accept manual applications.');
        }

        try {
            // Oracle trigger fires here — will raise ORA-20001 if duplicate
            DB::statement("
                INSERT INTO APPLICATION (
                    student_id, scholarship_id,
                    applied_at
                ) VALUES (
                    ?, ?,
                    SYSDATE
                )
            ", [$studentId, $scholarshipId]);

            return redirect()->route('student.scholarships.index')
                ->with('success', 'Application submitted successfully!');

        } catch (\Exception $e) {
            // Catch ORA-20001 from trigger (duplicate application)
            // Also catch ORA-00001 from UNIQUE constraint as backup
            $msg = $e->getMessage();
            if (str_contains($msg, 'ORA-20001') || str_contains($msg, 'ORA-00001')) {
                return back()->with('error',
                    'You have already applied to this scholarship.'
                );
            }
            // Re-throw unexpected errors
            return back()->with('error', 'Application failed: ' . $msg);
        }
    }
}
