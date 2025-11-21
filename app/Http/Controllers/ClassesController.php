<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classes::with('teacher','students')->get(); // include teacher if related
        return response()->json([
            'data' => $classes
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "class_id" => "required|string|max:25|unique:classes,class_id",
            "class_name" => "required|string|max:50",
            "user_id" => "required"
        ]);

        $classes = Classes::create($validated);

        return response()->json([
            'message' => 'Class created successfully!',
            'data' => $classes
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $class_id)
{
    $classes = Classes::with('teacher', 'students')->find($class_id);

    if (!$classes) {
        return response()->json(['message' => 'Class not found!'], 404);
    }

    return response()->json(['data' => $classes], 200);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $class_id)
    {
        $classes = Classes::find($class_id);

        if (!$classes) {
            return response()->json(['message' => 'Class not found'], 404);
        }

        $validated = $request->validate([
            "class_name" => "sometimes|string|max:50",
            "user_id" => "sometimes|exists:users,id"
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
