<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $data['title'] = 'Employees';
        $data['content_header'] = 'Employees';
        $data['employees'] = Employee::with(['department'])->get();
        return view('admin.employees.index', $data);
    }

    public function datatable()
    {
        $data = Employee::with(['department'])->get();
        return datatables($data)
            ->editColumn('department', function ($item) {
                return $item->department->name;
            })
            ->editColumn('image', function ($item){
                $html = '<img src="'.asset("storage/employee/$item->id/$item->image").'" style="max-height: 100px" class="rounded mx-auto d-block"/>';
                return $html;
            })
            ->editColumn('updated_at', function ($item){
                return $item->updated_at->format('F j, Y, g:i a');
            })
            ->addColumn('action', function ($item){
                $html = '<a href="'.route('employee.edit', ['id' => $item->id]).'" class="btn-primary mr-2 btn-sm btn"><i class="fas fa-edit"></i></a>';
                $html .= '<button type="button" onclick=delete_action('.$item->id.') class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal_delete"><i class="fas fa-trash-alt"></i></button>';
                return $html;
            })
            ->rawColumns(['action', 'image'])
            ->toJson();
    }

    public function create()
    {
        $data['title'] = 'Add Employee';
        $data['content_header'] = 'Add Employee';
        $data['departments'] = Department::all();
        return view('admin.employees.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'cnic' => ['required', 'unique:employees', 'size:15', 'regex:/^([0-9]{5})[\-]([0-9]{7})[\-]([0-9]{1})+/'],
            'department' => ['required', 'not_in:0'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png','max:2048'],
        ]);

        if ($request->hasFile('image')){
            $file = $request->file('image');
//            $fileName = Str::title($request->name).'.'.$file->getClientOriginalExtension();
            $fileName = Str::title($request->name).'.jpg';

            $employee = new Employee();
            $employee->name = $request->name;
            $employee->cnic = $request->cnic;
            $employee->department_id = $request->department;
            $employee->image = $fileName;
            $employee->save();
            $lastId = $employee->id;
            $fileNameForPython = $lastId.'_'.$fileName;
            $file->storeAs('',$fileNameForPython,'python-images');
            $file->storeAs('public/employee/'.$lastId, $fileName);
            return redirect()->route('employee.index')->with('success', 'Employee Added successfully1');
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Employee';
        $data['content_header'] = 'Edit Employee';
        $data['employee'] = Employee::with(['department'])->where('id', $id)->first();
        $data['departments'] = Department::all();
        return view('admin.employees.edit', $data);
    }

    public function update(Request $request)
    {
        $employee = Employee::where('id', $request->id)->first();
        $request->validate([
            'name' => ['required'],
            'cnic' => ['required', 'unique:employees,cnic,'.$employee->id, 'size:15', 'regex:/^([0-9]{5})[\-]([0-9]{7})[\-]([0-9]{1})+/'],
            'department' => ['required', 'not_in:0'],
        ]);
        if ($request->hasFile('image')){
            $request->validate([
                'image' => ['required', 'image', 'mimes:jpeg,jpg,png','max:2048'],
            ]);
            $file = $request->file('image');
            if (Storage::disk('python-images')->exists($employee->id.'_'.$employee->image)){
                Storage::disk('python-images')->delete($employee->image);
            }
            Storage::deleteDirectory('public/employee/'.$request->id);
//            $fileName = Str::title($request->name).'.'.$file->getClientOriginalExtension();
            $fileName = Str::title($request->name).'.jpg';
            $fileNameForPython = $employee->id.'_'.Str::title($request->name).'.jpg';
            $file->storeAs('',$fileNameForPython,'python-images');
            $file->storeAs('public/employee/'.$request->id, $fileName);
        } elseif ($request->name !== $employee->name){
            $fileName = Str::title($request->name).'.'.File::extension($employee->image);
            $fileNameForPython = $employee->id.'_'.Str::title($request->name).'.'.File::extension($employee->image);
            Storage::disk('python-images')->move($employee->id.'_'.$employee->image, $fileNameForPython);
            Storage::move('public/employee/'.$employee->id.'/'.$employee->image, 'public/employee/'.$employee->id.'/'.$fileName);
        } else {
            $fileName = $request->oldImage;
        }
        $employee->name = $request->name;
        $employee->cnic = $request->cnic;
        $employee->department_id = $request->department;
        $employee->image = $fileName;
        $employee->save();
        return redirect()->route('employee.index')->with('success', 'Employee updated successfully!');
    }

    public function delete(Request $request)
    {
        $employee = Employee::where('id', $request->id)->first();
        if (!empty($employee)){
            if (Storage::disk('python-images')->exists($employee->id.'_'.$employee->image)){
                Storage::disk('python-images')->delete($employee->id.'_'.$employee->image);
            }
            Storage::deleteDirectory('public/employee/'.$employee->id);
            $employee->delete();
            return response()->json(['message' => 'Employee deleted successfully!']);
        } else {
            return response()->json(['message' => 'Employee does not exist!'], 404);
        }
    }
}
