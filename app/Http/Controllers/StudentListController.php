<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\StudentList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentListController extends Controller
{
    /**
     * List all students (Admin) or all students for the teacher (if user_id provided)
     */
    public function index(Request $request)
    {
        $user_id = $request->query('user_id'); // optional query param

        if ($user_id) {
            // Get classes for the teacher
            $classes = Classes::with('students')->where('user_id', $user_id)->get();

            // Flatten all students into one array
            $students = $classes->flatMap(function ($class) {
                return $class->students;
            });

            return response()->json([
                'success' => true,
                'data' => $students
            ], 200);
        }

        // Return all students (admin)
        return response()->json([
            'success' => true,
            'data' => StudentList::all()
        ], 200);
    }

    /**
     * Get students by teacher ID
     */
    public function getByTeacher($user_id)
    {
        $classes = Classes::with('students')->where('user_id', $user_id)->get();
        $students = $classes->flatMap(function ($class) {
            return $class->students;
        });

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Store a new student (only in teacher's classes)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => "required",
            'student_name' => 'required|string|max:255',
            'class_id' => 'required|integer|exists:classes,class_id',
            'gender' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'class_id' => 'required|string|max:50'
        ]);

        // $class = Classes::where('class_id', $request->class_id)
        //                 ->where('user_id', Auth::id())
        //                 ->first();

        // if (!$class) {
        //     return response()->json([
        //         'message' => 'You are not allowed to add students to this class.'
        //     ], Response::HTTP_FORBIDDEN);
        // }

        // $student = StudentList::create([
        //     'student_name' => $request->student_name,
        //     'class_id' => $request->class_id,
        // ]);
        $student = StudentList::create($validated);

        return response()->json([
            'message' => 'Student added successfully.',
            'data' => $student
        ], Response::HTTP_CREATED);
    }

    /**
     * Show a student by ID with class & attendance summary
     */
    public function show($student_id)
    {
        $student = StudentList::with(['classes', 'attendanceSummary'])
                              ->where('student_id', $student_id)
                              ->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ], Response::HTTP_OK);
    }

    /**
     * Update a student (teacher can only update their own class students)
     */
    public function update(Request $request, $student_id)
    {
        $student = StudentList::find($student_id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if class belongs to logged-in teacher
        $class = Classes::where('class_id', $student->class_id)
                        ->where('user_id', $request->user()->id)
                        ->first();

        if (!$class) {
            return response()->json([
                'message' => 'You are not allowed to update this student.'
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'student_name' => 'sometimes|required|string|max:255',
            'gender' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'class_id' => 'required|string|max:50'
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'data' => $student
        ], Response::HTTP_OK);
    }

    /**
     * Delete a student (teacher can only delete their own class students)
     */
    public function destroy(Request $request, $student_id)
    {
        $student = StudentList::find($student_id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        // Check class ownership
        $class = Classes::where('class_id', $student->class_id)
                        ->where('user_id', $request->user()->id)
                        ->first();

        if (!$class) {
            return response()->json([
                'message' => 'You are not allowed to delete this student.'
            ], Response::HTTP_FORBIDDEN);
        }

        $student->delete();

        return response()->json(['message' => 'Student deleted successfully'], Response::HTTP_OK);
    }
}
