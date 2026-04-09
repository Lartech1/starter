<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\ClientVisitController;
use App\Http\Controllers\Api\PropertyAssignmentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ProjectUpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Properties (Estate Manager, Admin)
    Route::middleware('role:estate_manager,admin')->group(function () {
        Route::post('/properties', [PropertyController::class, 'store']);
        Route::put('/properties/{id}', [PropertyController::class, 'update']);
        Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
    });

    // Leads (Marketer, Realtor, Admin, Manager)
    Route::middleware('role:marketer,realtor,admin,manager')->group(function () {
        Route::get('/leads', [LeadController::class, 'index']);
        Route::post('/leads', [LeadController::class, 'store']);
        Route::put('/leads/{id}', [LeadController::class, 'update']);
        Route::post('/leads/{id}/assign', [LeadController::class, 'assignLead']);
    });

    // Projects (Admin, Manager, Field Agent)
    Route::middleware('role:admin,manager,field_agent')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::get('/projects/{id}', [ProjectController::class, 'show']);
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::put('/projects/{id}', [ProjectController::class, 'update']);
    });

    // Orders (Client, Manager, Admin)
    Route::middleware('role:client,manager,admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });

    // Documents (All authenticated users)
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroyRequest']);

    // Legal Officer specific
    Route::middleware('role:legal_officer,admin')->group(function () {
        Route::put('/documents/{id}/verify', [DocumentController::class, 'verify']);
    });

    // Messages (All authenticated users)
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/conversation/{userId}', [MessageController::class, 'getConversation']);
    Route::post('/messages', [MessageController::class, 'send']);
    Route::put('/messages/{id}/read', [MessageController::class, 'markAsRead']);

    // Leave Requests (All authenticated users)
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);

    // Manager/Admin specific for leave approval
    Route::middleware('role:manager,admin')->group(function () {
        Route::put('/leave-requests/{id}/approve', [LeaveRequestController::class, 'approve']);
    });

    // Client Visits (Realtor, Manager, Admin)
    Route::middleware('role:realtor,manager,admin')->group(function () {
        Route::get('/client-visits', [ClientVisitController::class, 'index']);
        Route::post('/client-visits', [ClientVisitController::class, 'store']);
        Route::put('/client-visits/{id}/approve', [ClientVisitController::class, 'approve']);
    });

    // Property Assignments (Estate Manager, Admin, Manager)
    Route::middleware('role:estate_manager,admin,manager')->group(function () {
        Route::post('/properties/{propertyId}/assign', [PropertyAssignmentController::class, 'assignToRealtor']);
    });

    // Realtor specific
    Route::middleware('role:realtor')->group(function () {
        Route::get('/my-assignments', [PropertyAssignmentController::class, 'getMyAssignments']);
    });

    // Attendance (All authenticated users)
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);

    // HR/Admin specific
    Route::middleware('role:hr,admin,manager')->group(function () {
        Route::get('/attendance-records', [AttendanceController::class, 'getAttendanceRecords']);
    });

    // Project Updates (Field Agent, Admin, Manager)
    Route::middleware('role:field_agent,admin,manager')->group(function () {
        Route::post('/projects/{projectId}/updates', [ProjectUpdateController::class, 'submitUpdate']);
        Route::get('/projects/{projectId}/updates', [ProjectUpdateController::class, 'getProjectUpdates']);
    });
});
