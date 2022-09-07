<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function index()
    {
        $data['title'] = 'Departments';
        $data['content_header'] = 'Departments';
        $data['departments'] = Department::with(['employees'])->get();
        return view('admin.departments.index', $data);
    }

    public function datatable()
    {
        $data = Department::all();
        return datatables($data)
            ->editColumn('updated_at', function ($item){
                return $item->updated_at->format('F j, Y, g:i a');
            })
            ->addColumn('action', function ($item){
                $html = '<button type="button" onclick="edit_action(this, '. $item->id .')" data-toggle="modal" data-target="#modal_edit" class="btn-primary mr-2 btn-sm btn"><i class="fas fa-edit"></i></button>';
                $html .= '<button type="button" onclick=delete_action('.$item->id.') class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal_delete"><i class="fas fa-trash-alt"></i></button>';
                return $html;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        $data['title'] = 'Add Department';
        $data['content_header'] = 'Add Department';
        return view('admin.departments.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'address' => ['required'],
        ]);

        $department = new Department();
        $department->name = $request->name;
        $department->address = $request->address;
        $department->save();

        return redirect()->route('department.index')->with('success', 'Department added successfully!');
    }

    public function edit($id)
    {
        return response()->json(['data' => Department::find($id)]);
    }

    public function update(Request $request)
    {
        if ($request->ajax()){
            $requestData = $request->all();
            $validator = Validator::make($requestData, [
                'name' => ['required'],
                'address' => ['required'],
            ]);
            if ($validator->fails()){
                return response()->json(['errors' => $validator->errors()->all()], 400);
            } else {
                $department = Department::where('id', $requestData['id'])->first();
                $department->name = $requestData['name'];
                $department->address = $requestData['address'];
                $department->save();
                return response()->json(['message' => 'Dpartment Updated Successfully']);
            }
        } else {
            return response()->json(['message' => 'Bad Request'], 409);
        }
    }

    public function delete(Request $request)
    {
        $department = Department::with(['employees'])->where('id', $request->id)->first();
        if (!empty($department)){
            $employees = $department->employees;
            if ($employees->count()){
                $employeIds = [];
                foreach ($employees as $employee){
                    if (Storage::exists('public/employee/'.$employee->id)
                        && Storage::disk('python-images')->exists($employee->image)){
                        Storage::deleteDirectory('public/employee/'.$employee->id);
                        Storage::disk('python-images')->delete($employee->image);
                    }
                    $employeIds[] = $employee->id;
                }
                Employee::whereIn('id', $employeIds)->delete();
            }
            $department->delete();
            return response()->json(['message' => 'Department with '. $employees->count() .' employees deleted successfully!']);
        } else {
            return response()->json(['message' => 'Department does not exist'], 404);
        }
    }
}
