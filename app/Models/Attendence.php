<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'status'
    ];

    public function student(){
        return $this->belongsTo(StudentList::class,'student_id');
    }

//     public function attendanceSummary()
// {
//     return $this->attendance()
//                 ->selectRaw('student_id, 
//                             SUM(status = "Present") as total_present,
//                             SUM(status = "Absent") as total_absent,
//                             SUM(status = "Late") as total_late')
//                 ->groupBy('student_id');
// }

}
