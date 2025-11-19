<?php

use App\Http\Controllers\AttendenceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\StudentListController;
use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('student_lists',StudentListController::class);

Route::delete('/student_lists/{student_id}',[StudentListController::class,'destroy']);

Route::apiResource('classes', ClassesController::class);
// Route::get('/students/teacher/{user_id}', [StudentListController::class, 'getByTeacher']);

Route::apiResource('attendences', AttendenceController::class);
Route::get('/attendences/{student_id}',[AttendenceController::class,'show']);

Route::apiResource('subjects', SubjectController::class);
Route::delete('/subjects/{subject_id}', [SubjectController::class,'destroy']);
// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);





