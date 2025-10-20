<?php

namespace App\Http\Controllers;

use App\Models\StudentList;
use Illuminate\Http\Request;

class StudentListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all students
        return response()->json(StudentList::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            "student_id" => "required|string|max:25",
            "student_name" => "required|string|max:255",
            "gender" => "nullable|string|max:10",
            "email" => "nullable|string|max:255",
            "class_id" => "required|string|max:50"
        ]);

        // Create record
        $student = StudentList::create($validated);

        return response()->json([
            'message' => 'Student created successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = StudentList::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return response()->json($student = StudentList::with('class','attendanceSummary')->get());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = StudentList::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            "student_name" => "sometimes|required|string|max:255",
            "gender" => "nullable|string|max:10",
            "email" => "nullable|email|max:255",
            "class_id" => "required|string|max:50"
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($student_id)
    {
        $student = StudentList::find($student_id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }
}
