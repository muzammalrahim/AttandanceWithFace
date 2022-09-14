<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
     public function dashboard()
    {
      $data['title'] = 'Dashboard';
      $data['content_header'] = 'Dashboard';
      $data['totalEmployees'] = count(Employee::all());
      $data['totalDepartments'] = count(Department::all());
      // $data['totalAtendee'] = count(Department::all());
      
      return view('admin.dashboard', $data);
    }
   
}
