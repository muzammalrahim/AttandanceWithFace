<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
class AssignKeyController extends Controller
{
    public function index(){
        // dd('welcome');
        $data['title'] = 'Assign keys';
        $data['content_header'] = 'Assign Keys';
        $data['departments'] = Department::all();
        return view('admin.assignkeys.index', $data);
    }
    public function datatable(Request $request)
    {
        $query = Attendance::query();
        if (!empty($request->departmentId) && $request->departmentId != 0){
            $query->join('employees as e', function ($eJoin) use ($request){
                $eJoin->on('attendances.employee_id', '=', 'e.id')
                // ->where('attendances.')
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
        } else {
            $query->join('employees as e', function ($eJoin){
                $eJoin->on('attendances.employee_id', '=', 'e.id')
                ->where('e.key_authority', 1)
                ->join('departments as d', function ($dJoin){
                    return $dJoin->on('e.department_id', '=', 'd.id');
                });
            });
        }
        $data = $query->get([
            'attendances.id', 'attendances.date', 'attendances.assign_key', 'attendances.recieve_key',
            'e.name as employeeName', 'e.cnic', 'e.key_authority', 'd.name as departmentName',
        ]);

        return datatables($data)
            ->editColumn('name', function ($item) {
                return $item->employeeName;
            })
            ->editColumn('cnic', function ($item) {
                return $item->cnic;
            })
             ->editColumn('key_authority', function ($item) {
                return $item->key_authority ? 'Yes' : 'No';
                // return '<span data-condition="'.$condition.'">'.$item->key_authority.'</span>';

            })
            ->editColumn('department', function ($item) {
//                $department = Department::where('id', $item->employee->department_id)->first();
                return $item->departmentName;
            })
             ->addColumn('action', function ($item){
                 $html = '<a href="'.route('onclick_assignKey', ['id' => $item->id]).'" class="btn-primary mr-2 btn-sm btn"><i class="fas fa-key"></i></a>';
                  $html .= '<a href="'.route('onclick_recieveKey', ['id' => $item->id]).'" class="btn-primary mr-2 btn-sm btn"><i class="fab fa-get-pocket"></i></a>';
                  return $html;
                // $html = '<a href="'.route('employee.edit', ['id' => $item->id]).'" class="btn-primary mr-2 btn-sm btn"><i class="fas fa-edit"></i></a>';
                // $html = '<button type="button" onclick=time() class="btn btn-sm btn-danger" data-toggle="" data-target=""><i class="fas fa-trash-alt"></i></button>';
                // return $html;
            })
            // ->addColumn('action', function ($item){
            //     $html = '<a href="'.route('employee.edit', ['id' => $item->id]).'" class="btn-primary mr-2 btn-sm btn"><i class="fas fa-edit"></i></a>';
            //     $html .= '<button type="button" onclick=delete_action('.$item->id.') class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal_delete"><i class="fas fa-trash-alt"></i></button>';
            //     return $html;
            // })
            // ->rawColumns(['action', 'key_authority'])
            ->toJson();
    }
    public function onclick_assignKey(Request $request){
        $assignKey = Attendance::find($request->id);
        $CurrentTime = date('h:i:s');
        $assignKey->assign_key = $CurrentTime;
        $assignKey->save();

        $data['title'] = 'Assign keys';
        $data['content_header'] = 'Assign Keys';
        $data['departments'] = Department::all();
        return view('admin.assignkeys.index', $data);        
    }
    public function onclick_recieveKey(Request $request){
        $recieveKey = Attendance::find($request->id);
        $CurrentTime = date('h:i:s');
        $recieveKey->recieve_key = $CurrentTime;
        $recieveKey->save();

        $data['title'] = 'Assign keys';
        $data['content_header'] = 'Assign Keys';
        $data['departments'] = Department::all();
        return view('admin.assignkeys.index', $data);        
    }
}
