<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\StudentList;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClassesController extends Controller
{

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Check how many classes this user already has
        $count = Classes::where('user_id', $user->id)->count();
        if ($count >= 8) {
            return response()->json([
                'message' => 'You can only create up to 8 classes.'
            ]);
        }

        $validated = $request->validate([
            "class_id" => "required|string|max:25|unique:classes,class_id",
            "class_name" => "required|string|max:50",
            "teach_days" => "required|json",
            "start_hour" => "required|time",
            "end_hour" => "required|time",
            "status" => "required|boolean",
        ]);

        $validated['user_id'] = $user->id;

        $classes = Classes::create($validated);

        return response()->json([
            'message' => 'Class created successfully!',
            'data' => $classes
        ], 201);
    }


    public function getByUser()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $classes = Classes::with('teacher', 'students')->where('user_id', $user->id)->get();
        return response()->json([
            "message " => "success",
            "data" => $classes
        ], 200);
    }

    public function getByClass($class_id)
{
    $userId = auth('api')->id();
    // Check if class exists AND belongs to this user
    $class = Classes::where('class_id', $class_id)
        ->where('user_id', $userId)
        ->with('students')
        ->first();

    if (!$class) {
        return response()->json([
            "message" => "Class not found or not owned by user"
        ], 404);
    }

    // Class belongs to user — return it
    return response()->json([
        "message" => "success",
        "class" => [
            "class_id" => $class->class_id,
            "class_name" => $class->class_name,
        ],
        "students" => $class->students 
    ], 200);
}



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $class_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $classes = Classes::where('user_id',$user->id)->find($class_id);
        if (!$classes) {
            return response()->json(['message' => 'Class not found'], 404);
        }

        $validated = $request->validate([
            "class_name" => "sometimes|string|max:50",
            "status" => "required|boolean"
        ]);

        $classes->update($validated);

        return response()->json([
            'message' => 'Class updated successfully!',
            'data' => $classes
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $class_id)
    {
        $classes = Classes::find($class_id);

        if (!$classes) {
            return response()->json(['message' => 'Class not found!'], 404);
        }

        $classes->delete();

        return response()->json([
            'message' => 'Class deleted successfully!'
        ], 200);
    }
}
