<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        $data['title'] = 'Attendance';
        $data['content_header'] = 'Attendance';
        // $data['attendance'] = Attendance::with(['employee'])->get();
        return view('admin.attendance.index', $data);
    }

    public function datatable()
    {
        $data = Attendance::with(['employee'])->get();
        return datatables($data)
            ->editColumn('name', function ($item) {
                return $item->employee->name;
            })
            ->editColumn('cnic', function ($item) {
                return $item->employee->cnic;
            })
            ->editColumn('department', function ($item) {
                $department = Department::where('id', $item->employee->department_id)->first();
                return $department->name;
            })
            // ->addColumn('action', function ($item){
            //     $html = '<a href="'.route('employee.edit', ['id' => $item->id]).'" class="btn-primary mr-2 btn-sm btn"><i class="fas fa-edit"></i></a>';
            //     $html .= '<button type="button" onclick=delete_action('.$item->id.') class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal_delete"><i class="fas fa-trash-alt"></i></button>';
            //     return $html;
            // })
            // ->rawColumns(['action', 'image'])
            ->toJson();
    }

}

