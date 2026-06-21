<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table      = 'APPLICATION';
    protected $primaryKey = 'application_id';
    public    $timestamps = false;

    protected $fillable = [
        'student_id',
        'scholarship_id',
        'application_date',
        'status',
        'merit_score',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function scholarship()
    {
        return $this->belongsTo(ScholarshipProgram::class, 'scholarship_id', 'scholarship_id');
    }

    public function allocation()
    {
        return $this->hasOne(Allocation::class, 'application_id', 'application_id');
    }
}
