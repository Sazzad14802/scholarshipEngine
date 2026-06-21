<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    /**
     * Show the scholarship creation form.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.scholarships.create', compact('departments'));
    }

    /**
     * Save a new scholarship to the database.
     * Uses scholarship_seq.NEXTVAL for the PK (Oracle sequence).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                      => 'required|string|max:255',
            'description'                => 'nullable|string|max:2000',
            'recipient_count'            => 'required|integer|min:1',
            'application_required'       => 'required|in:0,1',
            'status'                     => 'required|in:active,inactive,closed',
            'min_cgpa'                   => 'nullable|numeric|min:0|max:4',
            'max_family_income'          => 'nullable|numeric|min:0',
            'gender_requirement'         => 'nullable|in:male,female',
            'department_id'              => 'nullable|integer|exists:DEPARTMENT,department_id',
            'allow_existing_scholarship' => 'required|in:0,1',
        ]);

        $adminId = Auth::id();

        // Use a raw INSERT with Oracle sequence for the primary key
        DB::statement("
            INSERT INTO SCHOLARSHIP_PROGRAM (
                scholarship_id, created_by, department_id, title, description,
                recipient_count, application_required, status,
                min_cgpa, max_family_income, gender_requirement,
                allow_existing_scholarship
            ) VALUES (
                scholarship_seq.NEXTVAL, :created_by, :department_id, :title, :description,
                :recipient_count, :application_required, :status,
                :min_cgpa, :max_family_income, :gender_requirement,
                :allow_existing_scholarship
            )
        ", [
            'created_by'                  => $adminId,
            'department_id'               => $validated['department_id'] ?? null,
            'title'                        => $validated['title'],
            'description'                  => $validated['description'] ?? null,
            'recipient_count'              => $validated['recipient_count'],
            'application_required'         => $validated['application_required'],
            'status'                       => $validated['status'],
            'min_cgpa'                     => $validated['min_cgpa'] ?? null,
            'max_family_income'            => $validated['max_family_income'] ?? null,
            'gender_requirement'           => $validated['gender_requirement'] ?? null,
            'allow_existing_scholarship'   => $validated['allow_existing_scholarship'],
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Scholarship created successfully.');
    }
}
