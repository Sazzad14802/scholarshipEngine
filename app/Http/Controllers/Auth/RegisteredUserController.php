<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration.
     *
     * Admin  → username (any string) + name + password
     * Student → roll_number (BBDDRRR) + name + password + gender + cgpa + family_income
     *           Department + batch are auto-derived from roll number.
     */
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'STUDENT');

        // ── Base validation ────────────────────────────────────
        $rules = [
            'role'     => 'required|in:ADMIN,STUDENT',
            'name'     => 'required|string|max:150',
            'password' => 'required|string|min:1|confirmed',
        ];

        if ($role === 'STUDENT') {
            $rules['roll_number']   = ['required', 'string', 'regex:/^\d{7}$/', 'unique:USERS,username'];
            $rules['gender']        = 'required|in:male,female,other';
            $rules['cgpa']          = 'required|numeric|min:0|max:4';
            $rules['family_income'] = 'required|numeric|min:0';
        } else {
            $rules['username'] = 'required|string|max:50|unique:USERS,username';
        }

        $validated = $request->validate($rules);

        // ── Determine username and department ──────────────────
        if ($role === 'STUDENT') {
            $rollNumber = $validated['roll_number'];
            $username   = $rollNumber;

            // Parse department code from roll number (chars 3-4, 0-indexed 2-3)
            $deptCode = substr($rollNumber, 2, 2);

            $department = Department::where('dept_code', $deptCode)->first();
            if (! $department) {
                return back()->withErrors(['roll_number' => "No department found for code '{$deptCode}' in your roll number."])->withInput();
            }

            // Derive semester from batch year
            $batchYear = 2000 + (int) substr($rollNumber, 0, 2);
            $semester  = max(1, min(12, (date('Y') - $batchYear) * 2 - 1));
        } else {
            $username = $validated['username'];
        }

        // ── INSERT into USERS ──────────────────────────────────
        DB::statement(
            "INSERT INTO USERS (user_id, username, name, password_hash, role)
             VALUES (user_seq.NEXTVAL, :username, :name, :password_hash, :role)",
            [
                'username'      => $username,
                'name'          => $validated['name'],
                'password_hash' => $validated['password'],   // plain text
                'role'          => $role,
            ]
        );

        $user = User::where('username', $username)->firstOrFail();

        // ── INSERT into STUDENT (if student) ──────────────────
        if ($role === 'STUDENT') {
            DB::statement(
                "INSERT INTO STUDENT
                    (student_id, user_id, department_id, roll_number, gender, cgpa, family_income, semester)
                 VALUES
                    (student_seq.NEXTVAL, :user_id, :department_id, :roll_number,
                     :gender, :cgpa, :family_income, :semester)",
                [
                    'user_id'       => $user->user_id,
                    'department_id' => $department->department_id,
                    'roll_number'   => $rollNumber,
                    'gender'        => $validated['gender'],
                    'cgpa'          => $validated['cgpa'],
                    'family_income' => $validated['family_income'],
                    'semester'      => $semester,
                ]
            );
        }

        event(new Registered($user));
        Auth::login($user);

        return match ($role) {
            'ADMIN'   => redirect()->route('admin.dashboard'),
            'STUDENT' => redirect()->route('student.dashboard'),
            default   => redirect('/'),
        };
    }
}
