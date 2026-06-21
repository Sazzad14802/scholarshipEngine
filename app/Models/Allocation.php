<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $table      = 'ALLOCATION';
    protected $primaryKey = 'allocation_id';
    public    $timestamps = false;

    protected $fillable = [
        'application_id',
        'student_id',
        'scholarship_id',
        'allocation_date',
        'status',
        'amount',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id', 'application_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function scholarship()
    {
        return $this->belongsTo(ScholarshipProgram::class, 'scholarship_id', 'scholarship_id');
    }
}
