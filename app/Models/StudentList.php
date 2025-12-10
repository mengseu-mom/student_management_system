<?php

namespace App\Models;

use App\Http\Controllers\AttendenceController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentList extends Model
{
    use HasFactory;

    protected $table = 'student_lists';
    protected $primaryKey = 'student_id';
    public $incrementing = false; // because it's string
    protected $keyType = 'string';
    protected $fillable = ['student_id', 'student_name', 'gender', 'email','parent_contact', 'class_id'];

    // Relationship
    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendence::class, 'student_id', 'student_id');
    }

    public function attendanceSummary()
    {
        return $this->attendance()
            ->selectRaw('student_id, status, COUNT(*) as total')
            ->groupBy('student_id', 'status');
    }

    // Auto-generate student_id as string
    protected static function booted()
    {
        static::creating(function ($student) {
            if (!$student->student_id) {
                DB::transaction(function () use ($student) {
                    // get max numeric value of student_id
                    $maxId = DB::table('student_lists')
                        ->select(DB::raw('MAX(CAST(student_id AS INTEGER)) as max_id'))
                        ->value('max_id');

                    $student->student_id = $maxId ? (string)($maxId + 1) : '1';
                });
            }
        });
    }
}
