-- ============================================================
-- 01_create_tables.sql  (Schema V2 — Roll Number Auth)
-- Oracle XE 21c  |  Run as: system / 123
-- Drop everything and rebuild cleanly.
-- ============================================================

-- ── Drop tables (child → parent order) ────────────────────────
DROP TABLE ALLOCATION         CASCADE CONSTRAINTS PURGE;
DROP TABLE APPLICATION        CASCADE CONSTRAINTS PURGE;
DROP TABLE SCHOLARSHIP_PROGRAM CASCADE CONSTRAINTS PURGE;
DROP TABLE STUDENT            CASCADE CONSTRAINTS PURGE;
DROP TABLE DEPARTMENT         CASCADE CONSTRAINTS PURGE;
DROP TABLE USERS              CASCADE CONSTRAINTS PURGE;

-- ── Drop sequences ─────────────────────────────────────────────
DROP SEQUENCE user_seq;
DROP SEQUENCE department_seq;
DROP SEQUENCE student_seq;
DROP SEQUENCE scholarship_seq;
DROP SEQUENCE application_seq;
DROP SEQUENCE allocation_seq;

-- ── Sequences ─────────────────────────────────────────────────
CREATE SEQUENCE user_seq         START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE department_seq   START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE student_seq      START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE scholarship_seq  START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE application_seq  START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE allocation_seq   START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;

-- ── USERS ─────────────────────────────────────────────────────
-- username = 'admin' for admin, roll_number (e.g. 2207026) for students
CREATE TABLE USERS (
    user_id       NUMBER           NOT NULL,
    username      VARCHAR2(20)     NOT NULL,
    name          VARCHAR2(150)    NOT NULL,
    password_hash VARCHAR2(255)    NOT NULL,
    role          VARCHAR2(10)     NOT NULL,
    CONSTRAINT pk_users          PRIMARY KEY (user_id),
    CONSTRAINT uq_users_username UNIQUE (username),
    CONSTRAINT ck_users_role     CHECK (role IN ('ADMIN','STUDENT'))
);

-- ── DEPARTMENT ────────────────────────────────────────────────
-- dept_code = 2-digit code used in roll numbers (07 = CSE, 03 = EEE …)
-- capacity  = max students per batch
CREATE TABLE DEPARTMENT (
    department_id NUMBER        NOT NULL,
    name          VARCHAR2(150) NOT NULL,
    dept_code     VARCHAR2(2)   NOT NULL,
    capacity      NUMBER        NOT NULL,
    CONSTRAINT pk_department PRIMARY KEY (department_id),
    CONSTRAINT uq_dept_code  UNIQUE (dept_code),
    CONSTRAINT ck_dept_cap   CHECK (capacity > 0)
);

-- ── STUDENT ───────────────────────────────────────────────────
-- roll_number mirrors USERS.username; parsed for batch / dept / class roll
CREATE TABLE STUDENT (
    student_id    NUMBER        NOT NULL,
    user_id       NUMBER        NOT NULL,
    department_id NUMBER        NOT NULL,
    roll_number   VARCHAR2(10)  NOT NULL,
    gender        VARCHAR2(10)  NOT NULL,
    cgpa          NUMBER(4,2)   NOT NULL,
    family_income NUMBER(15,2)  NOT NULL,
    semester      NUMBER(2)     NOT NULL,
    CONSTRAINT pk_student        PRIMARY KEY (student_id),
    CONSTRAINT uq_student_roll   UNIQUE (roll_number),
    CONSTRAINT uq_student_user   UNIQUE (user_id),
    CONSTRAINT fk_student_user   FOREIGN KEY (user_id)        REFERENCES USERS(user_id),
    CONSTRAINT fk_student_dept   FOREIGN KEY (department_id)  REFERENCES DEPARTMENT(department_id),
    CONSTRAINT ck_student_gender CHECK (gender IN ('male','female','other')),
    CONSTRAINT ck_student_cgpa   CHECK (cgpa BETWEEN 0 AND 4),
    CONSTRAINT ck_student_sem    CHECK (semester BETWEEN 1 AND 12)
);

-- ── SCHOLARSHIP_PROGRAM ───────────────────────────────────────
CREATE TABLE SCHOLARSHIP_PROGRAM (
    scholarship_id NUMBER        NOT NULL,
    created_by     NUMBER        NOT NULL,
    department_id  NUMBER,
    title          VARCHAR2(255) NOT NULL,
    description    CLOB,
    amount         NUMBER(15,2)  NOT NULL,
    slots          NUMBER        NOT NULL,
    min_cgpa       NUMBER(4,2),
    max_income     NUMBER(15,2),
    deadline       DATE          NOT NULL,
    status         VARCHAR2(10)  DEFAULT 'OPEN',
    CONSTRAINT pk_scholarship      PRIMARY KEY (scholarship_id),
    CONSTRAINT fk_sch_created_by   FOREIGN KEY (created_by)    REFERENCES USERS(user_id),
    CONSTRAINT fk_sch_dept         FOREIGN KEY (department_id) REFERENCES DEPARTMENT(department_id),
    CONSTRAINT ck_sch_status       CHECK (status IN ('OPEN','CLOSED','AWARDED'))
);

-- ── APPLICATION ───────────────────────────────────────────────
CREATE TABLE APPLICATION (
    application_id  NUMBER      NOT NULL,
    student_id      NUMBER      NOT NULL,
    scholarship_id  NUMBER      NOT NULL,
    applied_at      DATE        DEFAULT SYSDATE,
    status          VARCHAR2(15) DEFAULT 'PENDING',
    CONSTRAINT pk_application      PRIMARY KEY (application_id),
    CONSTRAINT uq_app_student_sch  UNIQUE (student_id, scholarship_id),
    CONSTRAINT fk_app_student      FOREIGN KEY (student_id)     REFERENCES STUDENT(student_id),
    CONSTRAINT fk_app_scholarship  FOREIGN KEY (scholarship_id) REFERENCES SCHOLARSHIP_PROGRAM(scholarship_id),
    CONSTRAINT ck_app_status       CHECK (status IN ('PENDING','APPROVED','REJECTED'))
);

-- ── ALLOCATION ────────────────────────────────────────────────
CREATE TABLE ALLOCATION (
    allocation_id  NUMBER      NOT NULL,
    application_id NUMBER      NOT NULL,
    allocated_at   DATE        DEFAULT SYSDATE,
    amount         NUMBER(15,2) NOT NULL,
    remarks        VARCHAR2(500),
    CONSTRAINT pk_allocation     PRIMARY KEY (allocation_id),
    CONSTRAINT uq_alloc_app      UNIQUE (application_id),
    CONSTRAINT fk_alloc_app      FOREIGN KEY (application_id) REFERENCES APPLICATION(application_id)
);

PROMPT Schema V2 created successfully.
