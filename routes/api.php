<?php

use App\Http\Controllers\AttendenceController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\StudentListController;
use App\Models\Attendence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('student_lists',StudentListController::class);

Route::delete('/student_lists/{student_id}',[StudentListController::class,'destroy']);

Route::apiResource('classes', ClassesController::class);

Route::apiResource('attendences', AttendenceController::class);
Route::get('/attendences/{student_id}',[AttendenceController::class,'show']);
