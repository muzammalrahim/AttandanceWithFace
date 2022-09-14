<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
     public function dashboard()
    {
      $data['title'] = 'Dashboard';
      $data['content_header'] = 'Dashboard';
      $departments = Department::all();
      $data['totalEmployees'] = count(Employee::all());
      $data['totalDepartments'] = count($departments);
      $data['totalAttendee'] = Attendance::where('date', Carbon::now()->format('d-m-Y'))->count();
      $data['totalKeyAssigned'] = Attendance::where('date', Carbon::now()->format('d-m-Y'))
          ->join('employees as e', function ($eJoin){
              $eJoin->on('attendances.employee_id', '=', 'e.id')
                  ->where('e.key_authority', 1);
          })->count();
      $departmentNamesForBarChart = $totalDepartmentPresentEmployees =
      $totalDepartmentTimedOutEmployees = [];
      foreach ($departments as $department){
          $departmentNamesForBarChart[] = $department->name;
          $totalDepartmentPresentEmployees[] = Attendance::where('date', Carbon::now()->format('d-m-Y'))
              ->whereNull('attendances.time_out')
              ->join('employees as e', function ($eJoin) use ($department){
                  $eJoin->on('attendances.employee_id', '=', 'e.id')
                      ->join('departments as d', function ($dJoin) use ($department){
                          $dJoin->on('e.department_id', '=', 'd.id')
                              ->where('d.id', $department->id);
                      });
              })->count();
          $totalDepartmentTimedOutEmployees[] = Attendance::where('date', Carbon::now()->format('d-m-Y'))
              ->whereNotNull('attendances.time_out')
              ->join('employees as e', function ($eJoin) use ($department){
                  $eJoin->on('attendances.employee_id', '=', 'e.id')
                      ->join('departments as d', function ($dJoin) use ($department){
                          $dJoin->on('e.department_id', '=', 'd.id')
                              ->where('d.id', $department->id);
                      });
              })->count();
      }
      $data['totalDepartmentPresentEmployees'] = $totalDepartmentPresentEmployees;
      $data['departmentNamesForBarChart'] = $departmentNamesForBarChart;
      $data['totalDepartmentTimedOutEmployees'] = $totalDepartmentTimedOutEmployees;
      return view('admin.dashboard', $data);
    }

}
