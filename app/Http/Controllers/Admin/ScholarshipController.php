<?php
// File: app/Http/Controllers/Admin/ScholarshipController.php
//
// Full CRUD for SCHOLARSHIP_PROGRAM.
// index() and show() read from V_SCHOLARSHIP_SUMMARY (Oracle view).
// create() / store() unchanged from Week 1.
// update() uses raw DB::statement() — no Eloquent save().
// destroy() deletes scholarship and associated applications.
//
// Oracle note: DB::select() returns lowercase column names via PDO OCI8.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────────
    // Reads from V_SCHOLARSHIP_SUMMARY.
    // Supports GET filters: ?department_id=7&application_required=1
    public function index(Request $request)
    {
        $departments = DB::select('SELECT department_id, name FROM DEPARTMENT ORDER BY name');

        // Build WHERE clause dynamically (positional ? bindings for Oracle PDO)
        $whereParts = [];
        $bindings   = [];

        if ($request->filled('department_id')) {
            if ($request->department_id === 'all_depts') {
                $whereParts[] = "department_id IS NULL";
            } else {
                $whereParts[] = "department_id = ?";
                $bindings[]   = (int) $request->department_id;
            }
        }
        if ($request->filled('application_required')) {
            $whereParts[] = "application_required = ?";
            $bindings[]   = (int) $request->application_required;
        }

        $whereClause = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

        $scholarships = DB::select("
            SELECT scholarship_id, title, department_name, department_id,
                   recipient_count, application_required,
                   total_applications
            FROM   V_SCHOLARSHIP_SUMMARY
            {$whereClause}
            ORDER BY scholarship_id DESC
        ", $bindings);

        return view('admin.scholarships.index', compact('scholarships', 'departments'));
    }

    // ── SHOW ──────────────────────────────────────────────────────────
    public function show(int $id)
    {
        $rows = DB::select(
            'SELECT * FROM V_SCHOLARSHIP_SUMMARY WHERE scholarship_id = ?',
            [$id]
        );
        if (empty($rows)) {
            abort(404);
        }
        $scholarship = $rows[0];
        return view('admin.scholarships.show', compact('scholarship'));
    }

    // ── CREATE (form) ─────────────────────────────────────────────────
    public function create()
    {
        $departments = DB::select('SELECT * FROM DEPARTMENT ORDER BY name');
        return view('admin.scholarships.create', compact('departments'));
    }

    // ── STORE (save new) ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                      => 'required|string|max:255',
            'description'                => 'nullable|string|max:1000',
            'slots'                      => 'required|integer|min:1',
            'amount'                     => 'required|numeric|min:0',
            'deadline'                   => 'required|date',
            'application_required'       => 'required|in:0,1',
            'min_cgpa'                   => 'nullable|numeric|min:0|max:4',
            'max_income'                 => 'nullable|numeric|min:0',
            'gender_requirement'         => 'nullable|in:male,female',
            'department_id'              => 'nullable|integer|exists:DEPARTMENT,department_id',
            'allow_existing_scholarship' => 'required|in:0,1',
        ]);

        DB::statement("
            INSERT INTO SCHOLARSHIP_PROGRAM (
                created_by, department_id, title, description,
                amount, slots, min_cgpa, max_income, deadline,
                application_required, gender_requirement, allow_existing_scholarship
            ) VALUES (
                ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?
            )
        ", [
            Auth::id(),
            $validated['department_id'] ?? null,
            $validated['title'],
            $validated['description'] ?? null,
            $validated['amount'] ?? 0,
            $validated['slots'],
            $validated['min_cgpa'] ?? null,
            $validated['max_income'] ?? null,
            $validated['deadline'] ?? null,
            $validated['application_required'],
            $validated['gender_requirement'] ?? null,
            $validated['allow_existing_scholarship'],
        ]);

        return redirect()->route('admin.scholarships.index')
            ->with('success', 'Scholarship created successfully.');
    }

    // ── EDIT (form) ───────────────────────────────────────────────────
    public function edit(int $id)
    {
        $rows = DB::select(
            'SELECT * FROM SCHOLARSHIP_PROGRAM WHERE scholarship_id = ?',
            [$id]
        );
        if (empty($rows)) {
            abort(404);
        }
        $scholarship = $rows[0];
        $departments = DB::select('SELECT * FROM DEPARTMENT ORDER BY name');
        return view('admin.scholarships.edit', compact('scholarship', 'departments'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title'                      => 'required|string|max:255',
            'description'                => 'nullable|string|max:4000',
            'slots'                      => 'required|integer|min:1',
            'amount'                     => 'required|numeric|min:0',
            'deadline'                   => 'required|date',
            'application_required'       => 'required|in:0,1',
            'min_cgpa'                   => 'nullable|numeric|min:0|max:4',
            'max_income'                 => 'nullable|numeric|min:0',
            'gender_requirement'         => 'nullable|in:male,female',
            'department_id'              => 'nullable|integer|exists:DEPARTMENT,department_id',
            'allow_existing_scholarship' => 'required|in:0,1',
        ]);

        DB::statement("
            UPDATE SCHOLARSHIP_PROGRAM
               SET title                      = ?,
                   description                = ?,
                   slots                      = ?,
                   amount                     = ?,
                   deadline                   = ?,
                   application_required       = ?,
                   min_cgpa                   = ?,
                   max_income                 = ?,
                   gender_requirement         = ?,
                   department_id              = ?,
                   allow_existing_scholarship = ?
             WHERE scholarship_id             = ?
        ", [
            $validated['title'],
            $validated['description'] ?? null,
            $validated['slots'],
            $validated['amount'] ?? 0,
            $validated['deadline'] ?? null,
            $validated['application_required'],
            $validated['min_cgpa'] ?? null,
            $validated['max_income'] ?? null,
            $validated['gender_requirement'] ?? null,
            $validated['department_id'] ?? null,
            $validated['allow_existing_scholarship'],
            $id,
        ]);

        return redirect()->route('admin.scholarships.show', $id)
            ->with('success', 'Scholarship updated successfully.');
    }

    // ── DESTROY ───────────────────────────────────────────────────────
    // Deletes scholarship and its applications
    public function destroy(int $id)
    {
        $rows = DB::select(
            'SELECT scholarship_id FROM SCHOLARSHIP_PROGRAM WHERE scholarship_id = ?',
            [$id]
        );
        if (empty($rows)) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            DB::statement('DELETE FROM APPLICATION WHERE scholarship_id = ?', [$id]);
            DB::statement('DELETE FROM SCHOLARSHIP_PROGRAM WHERE scholarship_id = ?', [$id]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete scholarship.');
        }

        return redirect()->route('admin.scholarships.index')
            ->with('success', 'Scholarship deleted successfully.');
    }
}
