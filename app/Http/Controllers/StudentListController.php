<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\StudentList;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentListController extends Controller
{
    /**
     * List all students (Admin) or all students for the teacher (if user_id provided)
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Get classes for logged-in teacher
        $classes = Classes::with('students')->where('user_id', $user->id)->get();

        $students = $classes->flatMap(fn($class) => $class->students);

        return response()->json([
            'success' => true,
            'data' => $students
        ], 200);
    }

    /**
     * Get students by teacher ID
     */
    public function getByTeacher()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $classes = Classes::with('students')->where('user_id', $user->id)->get();

        $students = $classes->flatMap(fn($class) => $class->students);

        return response()->json([
            'success' => true,
            'data' => $students
        ], 200);
    }


    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'class_id' => 'required|integer|exists:classes,class_id',
            'gender' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'parent_contact' => 'nullable|string',
            'class_id' => 'required|string|max:50'
        ]);

        $class = Classes::where('class_id', $validated['class_id'])->where('user_id', $user->id)->first();
        if (!$class) {
            return response()->json([
                "message" => "you can't create studetn on this class"
            ],404);
        }
        $student = StudentList::create($validated);
        return response()->json([
            'message' => 'Student added successfully.',
            'data' => $student
        ], Response::HTTP_CREATED);
    }


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
            'parent_contact' => 'nullable|string',
            'class_id' => 'required|string|max:50'
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'data' => $student
        ], Response::HTTP_OK);
    }

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
