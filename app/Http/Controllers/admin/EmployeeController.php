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
                $html = '<img src="'.asset("storage/employee/$item->image").'" style="max-height: 100px" class="rounded mx-auto d-block"/>';
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
            $nameWithoutSpaces = preg_replace('/\s+/', '_', Str::title($request->name));
//            $fileName = Str::title($request->name).'.'.$file->getClientOriginalExtension();
            $fileName = $nameWithoutSpaces.'_'.$request->cnic.'.jpg';

            $employee = new Employee();
            $employee->name = $request->name;
            $employee->cnic = $request->cnic;
            $employee->department_id = $request->department;
            $employee->image = $fileName;
            $employee->save();
            $lastId = $employee->id;
            $fileNameForPython = $lastId.'_'.$nameWithoutSpaces.'.jpg';
            $file->storeAs('',$fileNameForPython,'python-images');
            $file->storeAs('public/employee/', $fileName);
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
        $nameWithoutSpaces = preg_replace('/\s+/', '_', Str::title($request->name));
        if ($request->hasFile('image')){
            $request->validate([
                'image' => ['required', 'image', 'mimes:jpeg,jpg,png','max:2048'],
            ]);
            $file = $request->file('image');
            $fileName = $nameWithoutSpaces.'_'.$request->cnic.'.jpg';
            $fileNameForPython = $employee->id.'_'.$nameWithoutSpaces.'.jpg';
            if (Storage::disk('python-images')->exists($fileNameForPython)){
                Storage::disk('python-images')->delete($fileNameForPython);
            }
            Storage::delete('public/employee/'.$fileName);
//            $fileName = Str::title($request->name).'.'.$file->getClientOriginalExtension();

            $file->storeAs('',$fileNameForPython,'python-images');
            $file->storeAs('public/employee/', $fileName);
        } elseif ($request->name !== $employee->name){
            $fileName = $nameWithoutSpaces.'_'.$employee->cnic.'.jpg';
            $fileNameForPython = $employee->id.'_'.$nameWithoutSpaces.'.jpg';
            $oldFileNameForPython = $employee->id.'_'.preg_replace('/\s+/', '_', Str::title($employee->name)).'.jpg';
            Storage::disk('python-images')->move($oldFileNameForPython, $fileNameForPython);
            Storage::move('public/employee/'.$employee->image, 'public/employee/'.$fileName);
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
            $nameWithoutSpaces = preg_replace('/\s+/', '_', Str::title($employee->name));
            $fileNameForPython = $employee->id.'_'.$nameWithoutSpaces.'.jpg';
            if (Storage::disk('python-images')->exists($fileNameForPython)){
                Storage::disk('python-images')->delete($fileNameForPython);
            }
            Storage::delete('public/employee/'.$employee->image);
            $employee->delete();
            return response()->json(['message' => 'Employee deleted successfully!']);
        } else {
            return response()->json(['message' => 'Employee does not exist!'], 404);
        }
    }

    public function import()
    {
        $data['title'] = 'Import Employees';
        $data['content_header'] = 'Import Employees';
        return view('admin.employees.import', $data);
    }

    public function importProcessing(Request $request)
    {
        // upload images
        if ($request->hasFile('employeeImages')){
            $zip = new \ZipArchive();
            $status = $zip->open($request->file('employeeImages')->getRealPath());
            if ($status !== true){
                throw new \Exception($status);
            } else {
                $tempStoragePath = storage_path('app/public/temp/employees/');
                if (!File::exists($tempStoragePath)){
                    File::makeDirectory($tempStoragePath,0755,true);
                }
                $zip->extractTo($tempStoragePath);
                $zip->close();
            }
        }

        // csv processing
        if ($request->hasFile('employeeCsv')){
            $csvData = array_map('str_getcsv', \file($request->employeeCsv));
            if (count($csvData) > 1){
                $pathTempEmployees = storage_path('app/public/temp');
                $tempFiles = File::allFiles($pathTempEmployees);
                foreach ($csvData as $key => $item){
                    if ($key==0){
                        continue;
                    }
                    $nameWithoutSpaces = preg_replace('/\s+/', '_', Str::title($item[1]));
                    $fileName = $nameWithoutSpaces.'_'.$item[2].'.jpg';
                    $employee = new Employee();
                    $employee->department_id = $item[0];
                    $employee->name = $item[1];
                    $employee->cnic = $item[2];
                    $employee->image = $fileName;
                    $employee->save();
                    $lastId = $employee->id;
                    foreach ($tempFiles as $tempFile){
                        if ($tempFile->getFilename() == $item[3]){
                            $fileNameTemp = $nameWithoutSpaces.'_'.$item[2].'.jpg';
                            $fileNameTempForPython = $lastId.'_'.$nameWithoutSpaces.'.jpg';
                            Storage::copy('public/temp/employees/'.$tempFile->getFilename(), 'public/employee/'.$fileNameTemp);
                            Storage::disk('python-images')->put($fileNameTempForPython, Storage::get('public/temp/employees/'.$tempFile->getFilename()));
                        }
                    }
                }
                Storage::deleteDirectory('public/temp/');
                return redirect()->route('employee.index')->with('success', count($csvData) . ' employees have been imported');
            } else {
                return back()->with('error', 'File has no data');
            }
        }
    }
}
