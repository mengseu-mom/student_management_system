<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classes;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    // List all teachers (users with role 'teacher')
    public function index()
    {
        $teachers = User::where('role', 'teacher')->with('classes')->get();
        return response()->json($teachers, 200);
    }

    // Show a single teacher
    public function show($user_id)
    {
        $teacher = User::where('role', 'teacher')->with('classes')->find($user_id);

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        return response()->json($teacher, 200);
    }

    // Create a new teacher
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'class_id' => 'sometimes|array', // optional array of class IDs
        ]);

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'teacher', // mark user as teacher
        ]);

        // Attach classes if provided
        if (isset($validated['class_id'])) {
            $teacher->classes()->sync($validated['class_id']);
        }

        return response()->json([
            'message' => 'Teacher created successfully',
            'data' => $teacher->load('classes')
        ], 201);
    }

    // Update teacher
    public function update(Request $request, $user_id)
    {
        $teacher = User::where('role', 'teacher')->find($user_id);

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $teacher->id,
            'password' => 'sometimes|string|min:6',
            'class_id' => 'sometimes|array',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $teacher->update($validated);

        if (isset($validated['class_id'])) {
            $teacher->classes()->sync($validated['class_id']);
        }

        return response()->json([
            'message' => 'Teacher updated successfully',
            'data' => $teacher->load('classes')
        ], 200);
    }

    // Delete teacher
    public function destroy($user_id)
    {
        $teacher = User::where('role', 'teacher')->find($user_id);

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        // Detach classes
        $teacher->classes()->detach();
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully'], 200);
    }
}
