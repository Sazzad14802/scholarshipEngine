<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipProgram extends Model
{
    protected $table      = 'SCHOLARSHIP_PROGRAM';
    protected $primaryKey = 'scholarship_id';
    public    $timestamps = false;

    protected $fillable = [
        'created_by',
        'department_id',
        'title',
        'description',
        'recipient_count',
        'application_required',
        'status',
        'min_cgpa',
        'max_family_income',
        'gender_requirement',
        'allow_existing_scholarship',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'scholarship_id', 'scholarship_id');
    }

    public function allocations()
    {
        return $this->hasMany(Allocation::class, 'scholarship_id', 'scholarship_id');
    }
}
