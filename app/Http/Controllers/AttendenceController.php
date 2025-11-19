<?php

namespace App\Http\Controllers;

use App\Models\Attendence;
use Illuminate\Http\Request;

class AttendenceController extends Controller
{
    public function store(Request $request){
        $validated = $request->validate([
            'student_id' => 'required|exists:student_lists,student_id',
            "date" => "required|date",
            'status' => 'required|in:Present,Absent,Late',
        ]);

        // $students_id = Attendence::where('student_id',$student_id)->find();

     
            $attendance = Attendence::create($validated);
        

        $total = Attendence::where('student_id',$request->student_id)->count();

        return response()->json([
            'message' => 'Attendance create successfully.',
            'total' => $total,
            'data' => $attendance
        ]);
    }

     public function show(string $student_id)
{
    // Get all attendance records for this student
    $attendance = Attendence::where('student_id', $student_id)->get();

    if ($attendance->isEmpty()) {
        return response()->json(['message' => 'No attendance records found for this student'], 404);
    }

    // Count totals for each status
    $totalPresent = $attendance->where('status', 'Present')->count();
    $totalAbsent  = $attendance->where('status', 'Absent')->count();
    $totalLate    = $attendance->where('status', 'Late')->count();

    // Return full summary
    return response()->json([
        'student_id' => $student_id,
        'total_records' => $attendance->count(),
        'summary' => [
            'Present' => $totalPresent,
            'Absent'  => $totalAbsent,
            'Late'    => $totalLate,
        ],
        'data' => $attendance
    ]);
}

public function index()
{
    $attendance = Attendence::with('student')
        ->get()
        ->groupBy('student_id')
        ->map(function ($records) {
            $summary = [
                'Present' => $records->where('status', 'Present')->count(),
                'Absent' => $records->where('status', 'Absent')->count(),
                'Late' => $records->where('status', 'Late')->count(),
            ];

            return [
                'student_id' => $records->first()->student_id,
                'student_name' => $records->first()->student->student_name ?? null,
                'gender' => $records->first()->student->gender ?? null,
                'summary' => $summary,
                'class_id' => $records->first()->student->class_id ?? null,
                'data' => $records->map(function ($record) {
                    return [
                        'date' => $record->created_at->toDateString(),
                        'status' => $record->status,
                    ];
                })->values(),
            ];
        })
        ->values(); // reset keys

    return response()->json($attendance, 200);
}


}
