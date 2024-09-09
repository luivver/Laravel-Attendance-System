<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LeaveController;

// Register page
Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
Route::post('/register', [RegisterController::class, 'register']);

// Login page (welcome page)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('log.login');
Route::post('/auth', [AuthController::class, 'login']);

// Middleware to ensure all following routes are protected
Route::middleware('auth')->group(function () {

    // Home page
    Route::get('/home/profile', [HomeController::class, 'profile'])->name('homepage.profile');
    // Route::get('/profile', function () {return view('homepage.profile');});
    Route::get('/homepage', [HomeController::class, 'index'])->name('homepage.home');
    Route::get('/home/late', [HomeController::class, 'late'])->name('homepage.late');
    Route::get('/download-late-records', [HomeController::class, 'downloadLateRecordsCsv'])->name('download.late.records');
    Route::get('/home/quota', [HomeController::class, 'quota'])->name('homepage.quota');
    Route::get('/download-quota-records', [HomeController::class, 'downloadQuotaRecordsCsv'])->name('download.quota.records');
    Route::get('/home/izin', [HomeController::class, 'izin'])->name('homepage.izin');
    Route::get('/download-leave-records', [HomeController::class, 'downloadLeaveRecordsCsv'])->name('download.leave.records');

    // Employee page
    Route::resource('/employees', EmployeeController::class);
    Route::get('/employees/download/records', [EmployeeController::class, 'downloadEmployeeCsv'])->name('employee.download.records');
    Route::get('/employees/cancel-import', [EmployeeController::class, 'cancelImportEmployee'])->name('employees.cancelImport');
    // to show employee name & position if employee_num is entered
    Route::get('/get-employee-details/{employee_num}', [EmployeeController::class, 'getEmployeeDetails']);

    // Attendance page
    Route::resource('/attendance', AttendanceController::class);
    Route::get('/attendance/download/records', [AttendanceController::class, 'downloadRecordsCsv'])->name('attendance.download.records');
    Route::get('/attendance/download/{fileName}', [AttendanceController::class, 'downloadFileRecordsCsv'])->name('attendance.download.file');
    Route::get('/attendance/refresh', [AttendanceController::class, 'refresh'])->name('attendance.refresh');
    Route::get('/attendance/cancel-import', [AttendanceController::class, 'cancelImportAttendance'])->name('attendance.cancelImport');

    // Schedules page
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');

    // Leave page (izin)
    Route::resource('leaves', LeaveController::class);
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::patch('/leaves/{id}/updateStatus', [LeaveController::class, 'updateStatus'])->name('leaves.updateStatus');
    Route::get('/leaves/{id}/preview', [LeaveController::class, 'show'])->name('leaves.printPreview');
    Route::get('/leaves/{id}/print', [LeaveController::class, 'printShow'])->name('leaves.print');
    Route::get('/leaves/{id}/previewTelat', [LeaveController::class, 'showTelat'])->name('leaves.printPreviewTelat');
    Route::get('/leaves/{id}/printTelat', [LeaveController::class, 'printShowTelat'])->name('leaves.printTelat');
    Route::get('/leaves/download/records', [LeaveController::class, 'downloadIzin'])->name('leaves.download.records');
    Route::delete('/leaves/{leaf}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

    // Cuti page
    Route::patch('/cuti/{id}/updateExpDate', [CutiController::class, 'updateExpDate'])->name('cuti.updateExpDate');
    Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('/', [EmployeeController::class, 'index'])->name('employees.test');