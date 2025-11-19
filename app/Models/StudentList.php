<?php

namespace App\Models;

use App\Http\Controllers\AttendenceController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentList extends Model
{
    use HasFactory;

    protected $table = 'student_lists';
    protected $primaryKey = 'student_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['student_id', 'student_name', 'gender', 'email', 'class_id'];

    // Relationship: Each student belongs to one class
    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
    // public function attendance()
    // {
    //     return $this->attendences()
    //         ->selectRaw('student_id, 
    //                          SUM(status = "Present") as total_present,
    //                          SUM(status = "Absent") as total_absent,
    //                          SUM(status = "Late") as total_late')
    //         ->groupBy('student_id');
    // }

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


}
