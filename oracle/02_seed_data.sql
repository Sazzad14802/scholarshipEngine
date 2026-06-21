-- ============================================================
-- 02_seed_data.sql  (Schema V2)
-- Departments + Admin + 1920 Students via PL/SQL loop
-- Oracle XE 21c  |  Run AFTER 01_create_tables.sql
-- ============================================================
SET DEFINE OFF;

-- ── 1. Departments ────────────────────────────────────────────
INSERT INTO DEPARTMENT (department_id, name, dept_code, capacity)
VALUES (department_seq.NEXTVAL, 'Electrical & Electronic Engineering', '03', 120);

INSERT INTO DEPARTMENT (department_id, name, dept_code, capacity)
VALUES (department_seq.NEXTVAL, 'Mechanical Engineering', '05', 120);

INSERT INTO DEPARTMENT (department_id, name, dept_code, capacity)
VALUES (department_seq.NEXTVAL, 'Computer Science & Engineering', '07', 120);

INSERT INTO DEPARTMENT (department_id, name, dept_code, capacity)
VALUES (department_seq.NEXTVAL, 'Electronics & Communication Engineering', '09', 60);

INSERT INTO DEPARTMENT (department_id, name, dept_code, capacity)
VALUES (department_seq.NEXTVAL, 'Biomedical Engineering', '11', 60);

COMMIT;

-- ── 2. Admin user ─────────────────────────────────────────────
INSERT INTO USERS (user_id, username, name, password_hash, role)
VALUES (user_seq.NEXTVAL, 'admin', 'System Administrator', '123', 'ADMIN');

COMMIT;

-- ── 3. Students — PL/SQL bulk insert ──────────────────────────
-- Batches 19-22 × 5 departments = 1920 students total
-- Roll format: BBDDRRR  (BB=batch, DD=dept_code, RRR=class roll)

DECLARE
    v_user_id   NUMBER;
    v_std_id    NUMBER;
    v_roll      VARCHAR2(10);
    v_gender    VARCHAR2(10);
    v_cgpa      NUMBER(4,2);
    v_income    NUMBER(15,2);
    v_semester  NUMBER(2);
    v_batch_str VARCHAR2(2);

BEGIN
    -- Outer loop: batches 19 → 22
    FOR batch IN 19..22 LOOP

        v_batch_str := LPAD(TO_CHAR(batch), 2, '0');

        -- Semester derived from batch (older batch = higher semester)
        v_semester := CASE batch
                          WHEN 22 THEN 5
                          WHEN 21 THEN 7
                          WHEN 20 THEN 8
                          WHEN 19 THEN 8
                      END;

        -- Middle loop: each department
        FOR dept IN (SELECT department_id, dept_code, capacity
                       FROM DEPARTMENT
                      ORDER BY dept_code) LOOP

            -- Inner loop: students 1 → dept.capacity
            FOR roll IN 1..dept.capacity LOOP

                v_roll   := v_batch_str || dept.dept_code || LPAD(TO_CHAR(roll), 3, '0');
                v_gender := CASE WHEN MOD(roll, 2) = 1 THEN 'male' ELSE 'female' END;
                v_cgpa   := ROUND(DBMS_RANDOM.VALUE(2.00, 4.00), 2);
                v_income := ROUND(DBMS_RANDOM.VALUE(5000, 200000) / 100) * 100;

                v_user_id := user_seq.NEXTVAL;
                v_std_id  := student_seq.NEXTVAL;

                INSERT INTO USERS (user_id, username, name, password_hash, role)
                VALUES (v_user_id, v_roll,
                        'Student ' || v_roll,
                        '123', 'STUDENT');

                INSERT INTO STUDENT
                    (student_id, user_id, department_id, roll_number,
                     gender, cgpa, family_income, semester)
                VALUES
                    (v_std_id, v_user_id, dept.department_id, v_roll,
                     v_gender, v_cgpa, v_income, v_semester);

            END LOOP; -- roll
        END LOOP; -- dept

        DBMS_OUTPUT.PUT_LINE('Batch ' || v_batch_str || ' inserted.');
    END LOOP; -- batch

    COMMIT;
    DBMS_OUTPUT.PUT_LINE('Done. Total users: ' || TO_CHAR(user_seq.CURRVAL));

EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        DBMS_OUTPUT.PUT_LINE('ERROR: ' || SQLERRM);
        RAISE;
END;
/

-- ── 4. Sample Scholarship Programs ────────────────────────────
DECLARE
    v_admin_id NUMBER;
    v_cse_id   NUMBER;
BEGIN
    SELECT user_id       INTO v_admin_id FROM USERS WHERE username = 'admin';
    SELECT department_id INTO v_cse_id   FROM DEPARTMENT WHERE dept_code = '07';

    -- Open scholarship for all departments
    INSERT INTO SCHOLARSHIP_PROGRAM
        (scholarship_id, created_by, department_id, title, description,
         amount, slots, min_cgpa, max_income, deadline, status)
    VALUES
        (scholarship_seq.NEXTVAL, v_admin_id, NULL,
         'Merit Scholarship 2024',
         'Awarded to top-performing students across all departments.',
         15000, 20, 3.50, 100000,
         TO_DATE('2024-12-31','YYYY-MM-DD'), 'OPEN');

    -- CSE-specific scholarship
    INSERT INTO SCHOLARSHIP_PROGRAM
        (scholarship_id, created_by, department_id, title, description,
         amount, slots, min_cgpa, max_income, deadline, status)
    VALUES
        (scholarship_seq.NEXTVAL, v_admin_id, v_cse_id,
         'CSE Excellence Award',
         'For high-achieving CSE students with financial need.',
         20000, 10, 3.25, 80000,
         TO_DATE('2024-11-30','YYYY-MM-DD'), 'OPEN');

    -- Need-based scholarship for all
    INSERT INTO SCHOLARSHIP_PROGRAM
        (scholarship_id, created_by, department_id, title, description,
         amount, slots, min_cgpa, max_income, deadline, status)
    VALUES
        (scholarship_seq.NEXTVAL, v_admin_id, NULL,
         'Financial Need Grant',
         'Priority support for students from low-income families.',
         10000, 50, 2.00, 40000,
         TO_DATE('2025-01-31','YYYY-MM-DD'), 'OPEN');

    COMMIT;
    DBMS_OUTPUT.PUT_LINE('Sample scholarships inserted.');
END;
/

PROMPT Seed V2 complete.
