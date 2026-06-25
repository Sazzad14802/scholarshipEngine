-- ============================================================
-- 03_student_reports.sql  (Student Management Reports)
-- Oracle XE 21c  |  Run AFTER 02_seed_data.sql
-- Demonstrates: WHERE, BETWEEN, ORDER BY, GROUP BY,
--               Aggregate Functions, JOINs
-- ============================================================

-- ── Report 1: Department-wise Student Count ────────────────────
-- Uses: JOIN, GROUP BY, COUNT aggregate
-- Shows how many students are enrolled in each department
SELECT
    d.name                          AS department_name,
    d.dept_code,
    COUNT(s.student_id)             AS total_students,
    ROUND(AVG(s.cgpa), 2)           AS avg_cgpa,
    ROUND(AVG(s.family_income), 2)  AS avg_family_income
FROM
    DEPARTMENT d
    JOIN STUDENT s ON s.department_id = d.department_id
GROUP BY
    d.name, d.dept_code
ORDER BY
    total_students DESC;


-- ── Report 2: Batch-wise Student Count ────────────────────────
-- Uses: SUBSTR (roll number parsing), GROUP BY, COUNT, AVG
-- Batch is encoded in roll_number as first 2 digits (BB)
SELECT
    SUBSTR(s.roll_number, 1, 2)         AS batch,
    COUNT(s.student_id)                 AS total_students,
    ROUND(AVG(s.cgpa), 2)               AS avg_cgpa,
    ROUND(MIN(s.cgpa), 2)               AS min_cgpa,
    ROUND(MAX(s.cgpa), 2)               AS max_cgpa,
    ROUND(AVG(s.family_income), 2)      AS avg_family_income
FROM
    STUDENT s
GROUP BY
    SUBSTR(s.roll_number, 1, 2)
ORDER BY
    batch ASC;


-- ── Report 3: Batch × Department Cross-Analysis ───────────────
-- Uses: JOIN, SUBSTR, GROUP BY multiple columns, COUNT
SELECT
    SUBSTR(s.roll_number, 1, 2)     AS batch,
    d.name                          AS department_name,
    COUNT(s.student_id)             AS student_count,
    ROUND(AVG(s.cgpa), 2)           AS avg_cgpa
FROM
    STUDENT s
    JOIN DEPARTMENT d ON d.department_id = s.department_id
GROUP BY
    SUBSTR(s.roll_number, 1, 2),
    d.name
ORDER BY
    batch ASC, d.name ASC;


-- ── Report 4: Top 10 Students by CGPA ─────────────────────────
-- Uses: JOIN, ORDER BY DESC, FETCH FIRST (Oracle 12c+ row limiting)
SELECT * FROM (
    SELECT
        s.roll_number,
        u.name                      AS student_name,
        d.name                      AS department_name,
        SUBSTR(s.roll_number, 1, 2) AS batch,
        s.gender,
        s.cgpa,
        s.family_income,
        s.semester
    FROM
        STUDENT s
        JOIN USERS      u ON u.user_id       = s.user_id
        JOIN DEPARTMENT d ON d.department_id = s.department_id
    ORDER BY
        s.cgpa DESC
)
WHERE ROWNUM <= 10;


-- ── Report 5: Students Eligible for Merit Scholarship ─────────
-- Uses: WHERE, BETWEEN, JOIN, AND conditions
-- Eligibility: CGPA >= 3.50 AND family_income <= 100000
SELECT
    s.roll_number,
    u.name                          AS student_name,
    d.name                          AS department_name,
    SUBSTR(s.roll_number, 1, 2)     AS batch,
    s.gender,
    s.cgpa,
    s.family_income
FROM
    STUDENT s
    JOIN USERS      u ON u.user_id       = s.user_id
    JOIN DEPARTMENT d ON d.department_id = s.department_id
WHERE
    s.cgpa        >= 3.50
    AND s.family_income <= 100000
ORDER BY
    s.cgpa DESC, s.family_income ASC;


-- ── Report 6: Students Eligible for Financial Need Grant ──────
-- Uses: WHERE, BETWEEN, JOIN
-- Eligibility: CGPA >= 2.00 AND family_income <= 40000
SELECT
    s.roll_number,
    u.name                          AS student_name,
    d.name                          AS department_name,
    SUBSTR(s.roll_number, 1, 2)     AS batch,
    s.cgpa,
    s.family_income
FROM
    STUDENT s
    JOIN USERS      u ON u.user_id       = s.user_id
    JOIN DEPARTMENT d ON d.department_id = s.department_id
WHERE
    s.cgpa BETWEEN 2.00 AND 4.00
    AND s.family_income <= 40000
ORDER BY
    s.family_income ASC;


-- ── Report 7: Students Who Have Received a Scholarship ────────
-- Uses: JOIN (4 tables), WHERE, ORDER BY
SELECT
    s.roll_number,
    u.name                          AS student_name,
    d.name                          AS department_name,
    sp.title                        AS scholarship_name,
    al.amount                       AS allocated_amount,
    al.allocated_at
FROM
    STUDENT           s
    JOIN USERS        u  ON u.user_id        = s.user_id
    JOIN DEPARTMENT   d  ON d.department_id  = s.department_id
    JOIN APPLICATION  ap ON ap.student_id    = s.student_id
    JOIN ALLOCATION   al ON al.application_id= ap.application_id
    JOIN SCHOLARSHIP_PROGRAM sp ON sp.scholarship_id = ap.scholarship_id
WHERE
    ap.status = 'APPROVED'
ORDER BY
    al.allocated_at DESC;


-- ── Report 8: Department Gender Breakdown ─────────────────────
-- Uses: GROUP BY, COUNT with CASE (conditional aggregation)
SELECT
    d.name                                                  AS department_name,
    COUNT(s.student_id)                                     AS total_students,
    COUNT(CASE WHEN s.gender = 'male'   THEN 1 END)         AS male_count,
    COUNT(CASE WHEN s.gender = 'female' THEN 1 END)         AS female_count,
    ROUND(
        COUNT(CASE WHEN s.gender = 'female' THEN 1 END) * 100.0
        / COUNT(s.student_id), 1
    )                                                       AS female_pct
FROM
    DEPARTMENT d
    JOIN STUDENT s ON s.department_id = d.department_id
GROUP BY
    d.name
ORDER BY
    d.name;


-- ── Report 9: CGPA Distribution by Bracket ────────────────────
-- Uses: CASE expression, GROUP BY, COUNT
SELECT
    CASE
        WHEN s.cgpa >= 3.75 THEN 'A+ (3.75 - 4.00)'
        WHEN s.cgpa >= 3.50 THEN 'A  (3.50 - 3.74)'
        WHEN s.cgpa >= 3.25 THEN 'A- (3.25 - 3.49)'
        WHEN s.cgpa >= 3.00 THEN 'B+ (3.00 - 3.24)'
        WHEN s.cgpa >= 2.75 THEN 'B  (2.75 - 2.99)'
        WHEN s.cgpa >= 2.50 THEN 'B- (2.50 - 2.74)'
        ELSE                     'C  (< 2.50)'
    END                         AS cgpa_bracket,
    COUNT(*)                    AS student_count,
    ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 1) AS percentage
FROM
    STUDENT s
GROUP BY
    CASE
        WHEN s.cgpa >= 3.75 THEN 'A+ (3.75 - 4.00)'
        WHEN s.cgpa >= 3.50 THEN 'A  (3.50 - 3.74)'
        WHEN s.cgpa >= 3.25 THEN 'A- (3.25 - 3.49)'
        WHEN s.cgpa >= 3.00 THEN 'B+ (3.00 - 3.24)'
        WHEN s.cgpa >= 2.75 THEN 'B  (2.75 - 2.99)'
        WHEN s.cgpa >= 2.50 THEN 'B- (2.50 - 2.74)'
        ELSE                     'C  (< 2.50)'
    END
ORDER BY
    MIN(s.cgpa) DESC;


-- ── Report 10: Overall Statistics Summary ─────────────────────
-- Uses: COUNT, AVG, MIN, MAX, ROUND aggregate functions
SELECT
    COUNT(s.student_id)                 AS total_students,
    ROUND(AVG(s.cgpa), 3)               AS avg_cgpa,
    ROUND(MIN(s.cgpa), 2)               AS min_cgpa,
    ROUND(MAX(s.cgpa), 2)               AS max_cgpa,
    ROUND(AVG(s.family_income), 2)      AS avg_family_income,
    ROUND(MIN(s.family_income), 2)      AS min_family_income,
    ROUND(MAX(s.family_income), 2)      AS max_family_income
FROM
    STUDENT s;


-- ── Dynamic Filter Example (parameterized template) ───────────
-- Uses: WHERE, BETWEEN, AND, ORDER BY
-- Replace :dept_code, :batch, :gender, :min_cgpa, :max_cgpa,
--         :min_income, :max_income with actual values
/*
SELECT
    s.roll_number,
    u.name                          AS student_name,
    d.name                          AS department_name,
    d.dept_code,
    SUBSTR(s.roll_number, 1, 2)     AS batch,
    s.gender,
    s.cgpa,
    s.family_income,
    s.semester
FROM
    STUDENT s
    JOIN USERS      u ON u.user_id       = s.user_id
    JOIN DEPARTMENT d ON d.department_id = s.department_id
WHERE
    (:dept_code IS NULL OR d.dept_code = :dept_code)
    AND (:batch     IS NULL OR SUBSTR(s.roll_number, 1, 2) = :batch)
    AND (:gender    IS NULL OR s.gender = :gender)
    AND s.cgpa          BETWEEN :min_cgpa   AND :max_cgpa
    AND s.family_income BETWEEN :min_income AND :max_income
    AND (
        :search IS NULL
        OR UPPER(s.roll_number) LIKE '%' || UPPER(:search) || '%'
        OR UPPER(u.name)        LIKE '%' || UPPER(:search) || '%'
    )
ORDER BY
    s.cgpa DESC;
*/

PROMPT Student reports loaded successfully.
