<?php

use App\Http\Controllers\AttendenceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\StudentListController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sendOtp', [AuthController::class, 'sendOtp']);
Route::get('/holidays/{year}', [HolidayController::class, 'getHolidays']);

// Protected routes (JWT)
Route::middleware('auth:api')->group(function () {

    // Logged-in user info
    Route::get('/user', function () {
        return JWTAuth::parseToken()->authenticate();
    });

    // Students managed by logged-in teacher
    Route::get('/students/teacher', [StudentListController::class, 'getByTeacher']);
    Route::apiResource('student_lists', StudentListController::class);

    // Classes managed by logged-in teacher
    Route::get('/teacher/classes', [ClassesController::class, 'getByUser']);
    Route::apiResource('classes', ClassesController::class);
    Route::get('classes/{class_id}/students', [ClassesController::class, 'getByClass']);

    // Attendance for logged-in teacher
    Route::get('/attendences/user', [AttendenceController::class, 'allAttendance']);
    Route::apiResource('attendences', AttendenceController::class);

    // Subjects
    Route::apiResource('subjects', SubjectController::class);
});
