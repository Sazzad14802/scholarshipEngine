SET PAGESIZE 100
SET LINESIZE 200
COLUMN column_name FORMAT A40
COLUMN data_type FORMAT A30

PROMPT ===== APPLICATION columns =====
SELECT column_name, data_type, nullable
  FROM all_tab_columns
 WHERE table_name = 'APPLICATION'
 ORDER BY column_id;

PROMPT ===== STUDENT columns =====
SELECT column_name, data_type, nullable
  FROM all_tab_columns
 WHERE table_name = 'STUDENT'
 ORDER BY column_id;

PROMPT ===== USERS columns =====
SELECT column_name, data_type, nullable
  FROM all_tab_columns
 WHERE table_name = 'USERS'
 ORDER BY column_id;

PROMPT ===== DEPARTMENT columns =====
SELECT column_name, data_type, nullable
  FROM all_tab_columns
 WHERE table_name = 'DEPARTMENT'
 ORDER BY column_id;

EXIT;
