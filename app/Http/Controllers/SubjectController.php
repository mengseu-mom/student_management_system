<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subject = Subject::all();

        return response()->json($subject);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|string',
            'subject_name' => 'nullable|string'
        ]);

        try{
            $subject = Subject::create($validated);

            return response()->json([
                "message" => "subject create successfully",
                "data" => $subject
            ], 200);
        }catch(\Illuminate\Database\QueryException $e) {
            if($e->errorInfo[1] == 1062){
                return response()->json([
                    "message" => "Subject ID already exist"
                ],409);
            }

            return response()->json([
                "message" => "Database error occurred!",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $subject_id)
    {
        $subject = Subject::where('subject_id', $subject_id)->first();

        if (!$subject) {
            return response()->json(['message' => 'Subject not found!'], 404);
            
        } else {    
            return response()->json([$subject],200);
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
    public function destroy(string $subject_id)
{
    $subject = Subject::find($subject_id);

    if (!$subject) {
        return response()->json([
            "message" => "Subject not found!"
        ], 404);
    }

    $subject->delete();

    return response()->json([
        "message" => "Subject deleted successfully",
        "data" => $subject
    ], 200);
}

}
