<?php

namespace App\Http\Controllers;

use App\Models\Attendence;
use App\Models\Classes;
use App\Models\StudentList;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttendenceController extends Controller
{
    public function store(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $students = $request->all();
        $created = [];

        foreach ($students as $student) {
            // Validate each student
            $validated = Validator::make($student, [
                'student_id' => 'required|exists:student_lists,student_id',
                'date' => 'required|date',
                'status' => 'required|in:Present,P,Absent',
            ])->validate();

            $validated['user_id'] = $user->id;
            // Create attendance
            $attendance = Attendence::create($validated);
            $created[] = $attendance;
        }

        return response()->json([
            'message' => 'Attendance created successfully for all students.',
            'data' => $created,
            'total' => count($created)
        ]);
    }

    public function show()
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Get student ID based on authenticated user
        $student_id = $user->id;

        // Fetch attendance records for this student
        $attendance = Attendence::where('student_id', $student_id)
            ->orderBy('created_at', 'asc')
            ->get([
                'id',
                'student_id',
                'class_id',
                'status',
                'created_at'
            ]);

        if ($attendance->isEmpty()) {
            return response()->json(['message' => 'No attendance records found'], 404);
        }

        // Summary counts
        $totalPresent = $attendance->where('status', 'Present')->count();
        $totalAbsent  = $attendance->where('status', 'Absent')->count();
        $totalP       = $attendance->where('status', 'P')->count();

        return response()->json([
            'student_id'    => $student_id,
            'total_records' => $attendance->count(),
            'summary' => [
                'Present' => $totalPresent,
                'Absent'  => $totalAbsent,
                'P'       => $totalP,
            ],
            'data' => $attendance
        ]);
    }

    public function allAttendance()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $classIds = Classes::where('user_id', $user->id)->pluck('class_id');

        // Get all students for these classes + their attendance
        $students = StudentList::with('attendance')
            ->whereIn('class_id', $classIds)
            ->get();

        $result = $students->map(function ($student) {

            // All attendance records for this student
            $records = $student->attendance;

            // Summary
            $summary = [
                'Present' => $records->where('status', 'Present')->count(),
                'P'    => $records->where('status', 'P')->count(),
                'Absent'  => $records->where('status', 'Absent')->count(),
            ];

            return [
                'student_id'   => $student->student_id,
                'student_name' => $student->student_name,
                'gender'       => $student->gender,
                'class_id'     => $student->class_id,
                'summary'      => $summary,
                'data' => $records->map(function ($record) {
                    return [
                        'date'   => $record->created_at->toDateString(),
                        'status' => $record->status,
                    ];
                })->values(),
            ];
        });

        return response()->json($result, 200);
    }
}
