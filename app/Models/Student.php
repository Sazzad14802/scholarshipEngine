<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table      = 'STUDENT';
    protected $primaryKey = 'student_id';
    public    $timestamps = false;

    protected $fillable = [
        'user_id',
        'department_id',
        'roll_number',
        'gender',
        'cgpa',
        'family_income',
        'semester',
    ];

    // ── Roll Number Parsers ────────────────────────────────────
    // Roll format: BBDDRRR  (e.g. 2207026)
    //   BB  = batch year   (22 → admission 2022)
    //   DD  = dept code    (07 = CSE)
    //   RRR = class roll   (026)

    /** Full admission year, e.g. 2022 */
    public function getBatchYearAttribute(): int
    {
        return 2000 + (int) substr($this->roll_number, 0, 2);
    }

    /** 2-digit batch string, e.g. "22" */
    public function getBatchAttribute(): string
    {
        return substr($this->roll_number, 0, 2);
    }

    /** 2-digit department code from roll, e.g. "07" */
    public function getDeptCodeAttribute(): string
    {
        return substr($this->roll_number, 2, 2);
    }

    /** 3-digit class roll, e.g. "026" */
    public function getClassRollAttribute(): string
    {
        return substr($this->roll_number, 4, 3);
    }

    // ── Relationships ──────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'student_id', 'student_id');
    }

    public function allocations()
    {
        return $this->hasManyThrough(
            Allocation::class,
            Application::class,
            'student_id',
            'application_id',
            'student_id',
            'application_id'
        );
    }
}
