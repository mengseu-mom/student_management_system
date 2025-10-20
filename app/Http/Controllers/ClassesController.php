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
        $classes = Classes::all();
        return response()->json([
            'data' => $classes
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "class_id" => "required|string|max:25",
            "class_name" => "nullable|string|max:50",
            "teacher_name" => "nullable|string|max:50"
        ]);

        $classes = Classes::create($validated);

        return response()->json([
            'message' => 'Class created successfully',
            'data' => $classes
        ],200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $class_id)
    {
        $classes = Classes::find($class_id);
        if(!$classes){
            return  'Class not found!';
            
        }else{
            return response()->json([
                'data' => $classes
            ],200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $class_id)
    {
        $classes = Classes::find($class_id);
        if(!$classes){
            return "Not found class to delete";
        }else{
            $classes -> delete();
            return response()->json([
                'message' => 'Class Deleted successfully',
                'data' => $classes
            ],200);
        }
    }
}
