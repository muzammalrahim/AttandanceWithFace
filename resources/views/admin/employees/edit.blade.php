@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <div class="row">
        <div class="col">
            <h1>{{ $content_header }}</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="container">
        <form method="POST" action="{{ route('employee.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $employee->id }}">
            <div class="card card-secondary">
                <div class="card-header">
                    <p class="font-weight-bold"> Edit Employee </p>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="name" class="font-weight-normal"> Name </label>
                            <input type="text" value="{{ $employee->name }}" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Name">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="address" class="font-weight-normal"> CNIC </label>
                            <input type="text" value="{{ $employee->cnic }}" class="form-control @error('cnic') is-invalid @enderror" name="cnic" placeholder="10521-1247203-5">
                            @error('cnic')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="department" class="font-weight-normal"> Department </label>
                            <select name="department" class="form-control @error('department') is-invalid @enderror">
                                <option value="0">--Select Department--</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ $department->id == $employee->department->id ? 'selected' : '' }}
                                    >{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                         <div class="form-group col-6">
                            <label for="key_authority" class="font-weight-normal"> Key Authority </label>

                            <select name="key_authority" class="form-control">
                                @foreach($keyAuth as $key => $value)
                                    <option value="{{ $key }}" {{ $employee->key_authority == $key ? 'selected' : '' }}>{{  $value }}</option>
                                @endforeach
                            </select>
                         </div>
                        <div class="form-group col-8">
                            <label for="image" class="font-weight-normal"> Image </label>
                            <x-adminlte-input-file name="image" igroup-size="md" placeholder="Choose an image...">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-lightblue">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-file>
                        </div>
                        <div class="form-group col-4">
                            <img style="max-height: 200px" class="rounded mx-auto d-block" id="image_preview" src="{{ asset('storage/employee/'.$employee->image) }}" alt="{{ $employee->name }}">
                            <input type="hidden" name="oldImage" value="{{ $employee->image }}">
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('employee.index') }}" class="btn btn-danger btn-md">Cancel</a>
                            </div>
                            <div class="col text-right">
                                <button type="submit" class="btn btn-success btn-md">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop
@section('plugins.BsCustomFileInput', true)
@section('js')
    <script type="text/javascript">
        $(document).ready(function (e){
            $('#image').change(function (){
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
@stop
