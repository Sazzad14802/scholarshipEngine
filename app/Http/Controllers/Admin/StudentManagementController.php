<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StudentManagementController extends Controller
{
    // Department code → abbreviation map (mirrors oracle dept_code)
    private const DEPT_MAP = [
        '03' => 'EEE',
        '05' => 'ME',
        '07' => 'CSE',
        '09' => 'ECE',
        '11' => 'BME',
    ];

    // Sortable column whitelist → actual SQL expression
    private const SORT_COLUMNS = [
        'roll_number'   => 's.roll_number',
        'cgpa'          => 's.cgpa',
        'family_income' => 's.family_income',
        'batch'         => 'SUBSTR(s.roll_number, 1, 2)',
    ];

    /**
     * Display the student management page with filters, search,
     * sorting, statistics, and pagination.
     *
     * Oracle SQL used:
     *   - JOIN (STUDENT ⋈ USERS ⋈ DEPARTMENT ⋈ APPLICATION/ALLOCATION)
     *   - WHERE with BETWEEN for range filters
     *   - LIKE for search
     *   - ORDER BY with dynamic column and direction
     *   - OFFSET … ROWS FETCH NEXT … ROWS ONLY (Oracle 12c+)
     *   - GROUP BY + COUNT + AVG for statistics
     *
     * NOTE: Oracle PDO (yajra/laravel-oci8) supports named bindings
     * in DB::select() — we use :name style consistently.
     */
    public function index(Request $request)
    {
        // ── 1. Validate and read filter inputs ──────────────────
        $filters = $request->validate([
            'department'  => 'nullable|string|in:03,05,07,09,11',
            'batch'       => 'nullable|string|in:19,20,21,22',
            'gender'      => 'nullable|string|in:male,female',
            'min_income'  => 'nullable|numeric|min:0',
            'max_income'  => 'nullable|numeric|min:0',
            'min_cgpa'    => 'nullable|numeric|min:0|max:4',
            'max_cgpa'    => 'nullable|numeric|min:0|max:4',
            'search'      => 'nullable|string|max:100',
            'sort'        => 'nullable|string|in:roll_number,cgpa,family_income,batch',
            'direction'   => 'nullable|string|in:asc,desc',
            'per_page'    => 'nullable|integer|in:10,20,50,100',
        ]);

        $department = $filters['department']  ?? null;
        $batch      = $filters['batch']       ?? null;
        $gender     = $filters['gender']      ?? null;
        $minIncome  = (float)($filters['min_income']  ?? 0);
        $maxIncome  = (float)($filters['max_income']  ?? 999999999);
        $minCgpa    = (float)($filters['min_cgpa']    ?? 0);
        $maxCgpa    = (float)($filters['max_cgpa']    ?? 4);
        $search     = $filters['search']      ?? null;
        $sortKey    = $filters['sort']        ?? 'roll_number';
        $sortCol    = self::SORT_COLUMNS[$sortKey];
        $direction  = strtoupper($filters['direction'] ?? 'asc');
        $perPage    = (int)($filters['per_page'] ?? 20);
        $page       = max(1, (int)$request->get('page', 1));
        $offset     = ($page - 1) * $perPage;

        // ── 2. Build WHERE clause using positional bindings ──────
        // Oracle PDO works most reliably with positional ? bindings
        // for dynamic queries built this way.
        $whereParts = [];
        $bindings   = [];

        // Department filter — uses JOIN on DEPARTMENT.dept_code
        if ($department) {
            $whereParts[] = "d.dept_code = ?";
            $bindings[]   = $department;
        }

        // Batch filter — extract BB from BBDDRRR roll number via SUBSTR
        if ($batch) {
            $whereParts[] = "SUBSTR(s.roll_number, 1, 2) = ?";
            $bindings[]   = $batch;
        }

        // Gender filter
        if ($gender) {
            $whereParts[] = "s.gender = ?";
            $bindings[]   = $gender;
        }

        // CGPA range — uses BETWEEN operator
        $whereParts[] = "s.cgpa BETWEEN ? AND ?";
        $bindings[]   = $minCgpa;
        $bindings[]   = $maxCgpa;

        // Family income range — uses BETWEEN operator
        $whereParts[] = "s.family_income BETWEEN ? AND ?";
        $bindings[]   = $minIncome;
        $bindings[]   = $maxIncome;

        // Search — LIKE on roll_number OR student name (UPPER for case-insensitive)
        if ($search) {
            $whereParts[] = "(UPPER(s.roll_number) LIKE UPPER(?) OR UPPER(u.name) LIKE UPPER(?))";
            $bindings[]   = '%' . $search . '%';
            $bindings[]   = '%' . $search . '%';
        }

        $whereClause = count($whereParts)
            ? 'WHERE ' . implode(' AND ', $whereParts)
            : '';

        // ── 3. Count query (for pagination total) ───────────────
        $countSql = "
            SELECT COUNT(*) AS cnt
            FROM   STUDENT s
            JOIN   USERS      u ON u.user_id       = s.user_id
            JOIN   DEPARTMENT d ON d.department_id = s.department_id
            {$whereClause}
        ";
        $countResult = DB::select($countSql, $bindings);
        $total       = (int)($countResult[0]->cnt ?? 0);

        // ── 4. Main paginated query ──────────────────────────────
        // Uses:
        //   JOIN     : STUDENT ⋈ USERS ⋈ DEPARTMENT
        //   LEFT JOIN: APPLICATION + ALLOCATION to detect scholarship status
        //   WHERE    : dynamic filters (BETWEEN, LIKE, =)
        //   ORDER BY : dynamic column and direction
        //   OFFSET … FETCH NEXT : Oracle 12c+ pagination
        $mainSql = "
            SELECT
                s.student_id,
                s.roll_number,
                u.name                              AS student_name,
                d.name                              AS department_name,
                d.dept_code,
                SUBSTR(s.roll_number, 1, 2)         AS batch,
                s.gender,
                s.cgpa,
                s.family_income,
                s.semester,
                CASE
                    WHEN MAX(CASE WHEN ap.status = 'APPROVED'
                                   AND al.allocation_id IS NOT NULL
                             THEN 1 ELSE 0 END) = 1 THEN 'Awarded'
                    WHEN MAX(CASE WHEN ap.status = 'PENDING' THEN 1 ELSE 0 END) = 1 THEN 'Pending'
                    WHEN MAX(CASE WHEN ap.status = 'REJECTED' THEN 1 ELSE 0 END) = 1 THEN 'Rejected'
                    ELSE 'None'
                END                                 AS scholarship_status
            FROM
                STUDENT s
                JOIN USERS      u  ON u.user_id       = s.user_id
                JOIN DEPARTMENT d  ON d.department_id = s.department_id
                LEFT JOIN APPLICATION  ap ON ap.student_id     = s.student_id
                LEFT JOIN ALLOCATION   al ON al.application_id = ap.application_id
            {$whereClause}
            GROUP BY
                s.student_id, s.roll_number, u.name, d.name, d.dept_code,
                SUBSTR(s.roll_number, 1, 2), s.gender, s.cgpa,
                s.family_income, s.semester
            ORDER BY
                {$sortCol} {$direction}
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
        ";

        $paginationBindings   = $bindings;
        $paginationBindings[] = $offset;
        $paginationBindings[] = $perPage;

        $students = DB::select($mainSql, $paginationBindings);

        // ── 5. Build LengthAwarePaginator ───────────────────────
        $paginator = new LengthAwarePaginator(
            $students,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );





        // ── 7. Prepare view data ─────────────────────────────────
        $deptOptions = [
            '03' => 'EEE',
            '05' => 'ME',
            '07' => 'CSE',
            '09' => 'ECE',
            '11' => 'BME',
        ];

        return view('admin.students.index', [
            'paginator'   => $paginator,
            'students'    => $students,
            'deptOptions' => $deptOptions,
            'filters'     => [
                'department' => $department,
                'batch'      => $batch,
                'gender'     => $gender,
                'minIncome'  => $minIncome,
                'maxIncome'  => $maxIncome,
                'minCgpa'    => $minCgpa,
                'maxCgpa'    => $maxCgpa,
                'search'     => $search,
                'direction'  => strtolower($filters['direction'] ?? 'asc'),
                'perPage'    => $perPage,
            ],
            'sortKey'     => $sortKey,
            'total'       => $total,
        ]);
    }
}
