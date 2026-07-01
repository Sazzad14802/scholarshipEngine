SET DEFINE OFF;

INSERT INTO DEPARTMENT (name, dept_code, capacity) VALUES ('Electrical and Electronic Engineering', '03', 120);
INSERT INTO DEPARTMENT (name, dept_code, capacity) VALUES ('Mechanical Engineering', '05', 120);
INSERT INTO DEPARTMENT (name, dept_code, capacity) VALUES ('Computer Science and Engineering', '07', 120);
INSERT INTO DEPARTMENT (name, dept_code, capacity) VALUES ('Electronics and Communication Engineering', '09', 60);
INSERT INTO DEPARTMENT (name, dept_code, capacity) VALUES ('Biomedical Engineering', '11', 60);

COMMIT;

INSERT INTO USERS (username, name, password, role) VALUES ('admin', 'System Administrator', '123', 'ADMIN');

COMMIT;

DECLARE
    TYPE name_list IS TABLE OF VARCHAR2(30);
    v_male_first_names name_list := name_list(
        'Rahim','Karim','Hasan','Sakib','Nafis',
        'Rakib','Shanto','Adnan','Rafi','Mahin',
        'Tamim','Arif','Imran','Fahim','Rashed',
        'Sohel','Mehedi','Jubayer','Anik','Sabbir'
    );
    v_female_first_names name_list := name_list(
        'Ayesha','Fatema','Nusrat','Mim','Tania',
        'Sumaiya','Sadia','Jannat','Maliha','Rima',
        'Farzana','Sabrina','Nabila','Tasnia','Raisa',
        'Anika','Lamia','Moumita','Sharmin','Priya'
    );
    v_last_names name_list := name_list(
        'Ahmed','Islam','Hossain','Rahman','Khan',
        'Akter','Sultan','Chowdhury','Biswas','Parvez',
        'Hasan','Kabir','Mahmud','Uddin','Sarker',
        'Roy','Paul','Das','Talukder','Sheikh'
    );

    v_user_id   NUMBER;
    v_std_id    NUMBER;
    v_roll      VARCHAR2(10);
    v_gender    VARCHAR2(10);
    v_cgpa      NUMBER(4,2);
    v_income    NUMBER(15,2);
    v_semester  NUMBER(2);
    v_batch_str VARCHAR2(2);
    v_rand      NUMBER;
    v_full_name VARCHAR2(150);
BEGIN
    FOR batch IN 19..22 LOOP
        v_batch_str := LPAD(TO_CHAR(batch), 2, '0');
        v_semester := CASE batch WHEN 22 THEN 5 WHEN 21 THEN 7 WHEN 20 THEN 8 WHEN 19 THEN 8 END;

        FOR dept IN (SELECT department_id, dept_code, capacity FROM DEPARTMENT ORDER BY dept_code) LOOP
            FOR roll IN 1..dept.capacity LOOP
                v_roll   := v_batch_str || dept.dept_code || LPAD(TO_CHAR(roll), 3, '0');

                v_rand := DBMS_RANDOM.VALUE(0, 100);

                v_gender := CASE
                                WHEN v_rand < 70 THEN 'male'
                                ELSE 'female'
                            END;
                            
                v_cgpa   := ROUND(DBMS_RANDOM.VALUE(2.00, 4.00), 2);
                v_income := ROUND(DBMS_RANDOM.VALUE(5000, 200000) / 100) * 100;
                
                IF v_gender = 'female' THEN
                    v_full_name := v_female_first_names(TRUNC(DBMS_RANDOM.VALUE(1, 21))) || ' ' || v_last_names(TRUNC(DBMS_RANDOM.VALUE(1, 21)));
                ELSE
                    v_full_name := v_male_first_names(TRUNC(DBMS_RANDOM.VALUE(1, 21))) || ' ' || v_last_names(TRUNC(DBMS_RANDOM.VALUE(1, 21)));
                END IF;

                INSERT INTO USERS (username, name, password, role)
                VALUES (v_roll, v_full_name, '123', 'STUDENT')
                RETURNING user_id INTO v_user_id;

                INSERT INTO STUDENT (user_id, department_id, roll_number, gender, cgpa, family_income, semester)
                VALUES (v_user_id, dept.department_id, v_roll, v_gender, v_cgpa, v_income, v_semester);
            END LOOP;
        END LOOP;
        DBMS_OUTPUT.PUT_LINE('Batch ' || v_batch_str || ' inserted.');
    END LOOP;
    COMMIT;
END;
/

DECLARE
    v_admin_id NUMBER;
    v_cse_id   NUMBER;
BEGIN
    SELECT user_id       INTO v_admin_id FROM USERS WHERE username = 'admin';
    SELECT department_id INTO v_cse_id   FROM DEPARTMENT WHERE dept_code = '07';

    INSERT INTO SCHOLARSHIP_PROGRAM (created_by, department_id, title, description, amount, slots, min_cgpa, max_income, deadline)
    VALUES (v_admin_id, NULL, 'Merit Scholarship 2024', 'Awarded to top-performing students across all departments.', 15000, 20, 3.50, 100000, TO_DATE('2024-12-31','YYYY-MM-DD'));

    INSERT INTO SCHOLARSHIP_PROGRAM (created_by, department_id, title, description, amount, slots, min_cgpa, max_income, deadline)
    VALUES (v_admin_id, v_cse_id, 'CSE Excellence Award', 'For high-achieving CSE students with financial need.', 20000, 10, 3.25, 80000, TO_DATE('2024-11-30','YYYY-MM-DD'));

    INSERT INTO SCHOLARSHIP_PROGRAM (created_by, department_id, title, description, amount, slots, min_cgpa, max_income, deadline)
    VALUES (v_admin_id, NULL, 'Financial Need Grant', 'Priority support for students from low-income families.', 10000, 50, 2.00, 40000, TO_DATE('2025-01-31','YYYY-MM-DD'));

    COMMIT;
END;
/
PROMPT Seed data inserted.
