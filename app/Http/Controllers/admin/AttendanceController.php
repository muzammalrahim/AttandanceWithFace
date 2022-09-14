<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
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
        $data['departments'] = Department::all();
        return view('admin.attendance.index', $data);
    }

    public function datatable(Request $request)
    {
        $query = Attendance::query();
        if (!empty($request->departmentId) && $request->departmentId != 0){
            $query->join('employees as e', function ($eJoin) use ($request){
                $eJoin->on('attendances.employee_id', '=', 'e.id')
                    ->join('departments as d', function ($dJoin) use ($request){
                        return $dJoin->on('e.department_id', '=', 'd.id')
                            ->where('d.id', $request->departmentId);
                    });
            });
        } else if (!empty($request->date)) {
            $query->where('attendances.date', $request->date)
                ->leftJoin('employees as e', function ($eJoin){
                    $eJoin->on('attendances.employee_id', '=', 'e.id')
                        ->leftJoin('departments as d', function ($dJoin){
                            return $dJoin->on('e.department_id', '=', 'd.id');
                        });
                });
        } else if (!empty($request->departmentId) && $request->departmentId != 0 && !empty($request->date)) {
            $query->where('attendances.date', $request->date)
                ->join('employees as e', function ($eJoin) use ($request){
                    $eJoin->on('attendances.employee_id', '=', 'e.id')
                        ->join('departments as d', function ($dJoin) use ($request){
                            return $dJoin->on('e.department_id', '=', 'd.id')
                                ->where('d.id', $request->departmentId);
                        });
                });
        } else {
            $query->where('attendances.date', Carbon::now()->format('d-m-Y'))
                ->leftJoin('employees as e', function ($eJoin){
                    $eJoin->on('attendances.employee_id', '=', 'e.id')
                        ->leftJoin('departments as d', function ($dJoin){
                            return $dJoin->on('e.department_id', '=', 'd.id');
                        });
                });
        }
        $data = $query->get([
            'attendances.id', 'attendances.date', 'attendances.time_in', 'attendances.time_out',
            'e.name as employeeName', 'e.cnic', 'd.name as departmentName',
        ]);
        return datatables($data)
            ->editColumn('name', function ($item) {
                return $item->employeeName;
            })
            ->editColumn('cnic', function ($item) {
                return $item->cnic;
            })
            ->editColumn('department', function ($item) {
//                $department = Department::where('id', $item->employee->department_id)->first();
                return $item->departmentName;
            })
            // ->addColumn('action', function ($item){
            //     $html = '<a href="'.route('employee.edit', ['id' => $item->id]).'" class="btn-primary mr-2 btn-sm btn"><i class="fas fa-edit"></i></a>';
            //     $html .= '<button type="button" onclick=delete_action('.$item->id.') class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal_delete"><i class="fas fa-trash-alt"></i></button>';
            //     return $html;
            // })
            // ->rawColumns(['action', 'image'])
            ->toJson();
    }

    public function attendanceFilter(Request $request)
    {
        return response()->json($request->all());
    }

}

