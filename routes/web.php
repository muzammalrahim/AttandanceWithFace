<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function (){
    return view('admin.login');
});

Auth::routes();

Route::get('/
    ', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function (){
    // Route::get('dashboard', function (){
    //     return view('admin.index');
    // })->name('admin.dashboard');

    // Dashboard
    Route::get('dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Departments
    Route::get('departments', [\App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('department.index');
    Route::get('departments/ajax/datatable', [\App\Http\Controllers\Admin\DepartmentController::class, 'datatable'])->name('department.datatable');
    Route::get('departments/create', [\App\Http\Controllers\Admin\DepartmentController::class, 'create'])->name('department.create');
    Route::post('departments/store', [\App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('department.store');
    Route::get('departments/ajax/edit/{id}', [\App\Http\Controllers\Admin\DepartmentController::class, 'edit'])->name('department.edit');
    Route::post('departments/ajax/update', [\App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('department.update');
    Route::post('departments/ajax/delete', [\App\Http\Controllers\Admin\DepartmentController::class, 'delete'])->name('department.delete');

    // Employees
    Route::get('employees', [\App\Http\Controllers\Admin\EmployeeController::class, 'index'])->name('employee.index');
    Route::get('employees/ajax/datatable', [\App\Http\Controllers\Admin\EmployeeController::class, 'datatable'])->name('employee.datatable');
    Route::get('employees/create', [\App\Http\Controllers\Admin\EmployeeController::class, 'create'])->name('employee.create');
    Route::post('employees/store', [\App\Http\Controllers\Admin\EmployeeController::class, 'store'])->name('employee.store');
    Route::get('employees/edit/{id}', [\App\Http\Controllers\Admin\EmployeeController::class, 'edit'])->name('employee.edit');
    Route::post('employees/update', [\App\Http\Controllers\Admin\EmployeeController::class, 'update'])->name('employee.update');
    Route::post('employees/ajax/delete', [\App\Http\Controllers\Admin\EmployeeController::class, 'delete'])->name('employee.delete');
    Route::get('employees/import', [\App\Http\Controllers\Admin\EmployeeController::class, 'import'])->name('employee.import');
    Route::post('employees/import/processing', [\App\Http\Controllers\Admin\EmployeeController::class, 'importProcessing'])->name('employee.import.processing');
    //Attendance
    Route::get('attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/ajax/datatable', [\App\Http\Controllers\Admin\AttendanceController::class, 'datatable'])->name('attendance.datatable');
    Route::post('attendance/ajax/filter', [\App\Http\Controllers\Admin\AttendanceController::class, 'attendanceFilter'])->name('attendance.filter');

     //Assign Keys
    Route::get('assignKeys', [\App\Http\Controllers\Admin\AssignKeyController::class, 'index'])->name('assignkey.index');
    Route::get('assignKeys/ajax/datatable', [\App\Http\Controllers\Admin\AssignKeyController::class, 'datatable'])->name('assignKeys.datatable');
    Route::get('onclick_assignKey', [\App\Http\Controllers\Admin\AssignKeyController::class, 'onclick_assignKey'])->name('onclick_assignKey');
    Route::get('onclick_recieveKey', [\App\Http\Controllers\Admin\AssignKeyController::class, 'onclick_recieveKey'])->name('onclick_recieveKey');

    // Route::get('assignKeys/givekey/{id}', [\App\Http\Controllers\Admin\AssignKeyController::class, 'givekey'])->name('assignKeys.givekey');



});
