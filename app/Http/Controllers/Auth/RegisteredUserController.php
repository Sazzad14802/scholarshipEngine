<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'STUDENT');

        $rules = [
            'role'     => 'required|in:ADMIN,STUDENT',
            'name'     => 'required|string|max:150',
            'password' => 'required|string|min:1|confirmed',
        ];

        if ($role === 'STUDENT') {
            $rules['roll_number']   = ['required', 'string', 'regex:/^\d{7}$/', 'unique:USERS,username'];
            $rules['gender']        = 'required|in:male,female';
            $rules['cgpa']          = 'required|numeric|min:0|max:4';
            $rules['family_income'] = 'required|numeric|min:0';
        } else {
            $rules['username'] = 'required|string|max:50|unique:USERS,username';
        }

        $validated = $request->validate($rules);

        if ($role === 'STUDENT') {
            $rollNumber = $validated['roll_number'];
            $username   = $rollNumber;
            $deptCode   = substr($rollNumber, 2, 2);

            $department = DB::selectOne('SELECT department_id FROM DEPARTMENT WHERE dept_code = ?', [$deptCode]);
            if (! $department) {
                return back()->withErrors(['roll_number' => "No department found for code '{$deptCode}' in your roll number."])->withInput();
            }

            $batchYear = 2000 + (int) substr($rollNumber, 0, 2);
            $semester  = max(1, min(12, (date('Y') - $batchYear) * 2 - 1));
        } else {
            $username = $validated['username'];
        }

        DB::statement(
            "INSERT INTO USERS (username, name, password, role)
             VALUES (:username, :name, :password, :role)",
            [
                'username'      => $username,
                'name'          => $validated['name'],
                'password'      => $validated['password'],
                'role'          => $role,
            ]
        );

        // Fetch the inserted user since username is unique
        $userRecord = DB::selectOne('SELECT * FROM USERS WHERE username = ?', [$username]);
        $user = new User((array) $userRecord);
        $user->user_id = $userRecord->user_id;

        if ($role === 'STUDENT') {
            DB::statement(
                "INSERT INTO STUDENT
                    (user_id, department_id, roll_number, gender, cgpa, family_income, semester)
                 VALUES
                    (:user_id, :department_id, :roll_number, :gender, :cgpa, :family_income, :semester)",
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
