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
{{--    @include('admin.errors')--}}
    <div class="container">
        <form method="POST" action="{{ route('employee.import.processing') }}" enctype="multipart/form-data">
            @csrf
            <div class="card card-secondary">
                <div class="card-header">
                    <p class="font-weight-bold"> Import Employees </p>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="importFile" class="font-weight-normal"> Employees Csv </label>
                            <x-adminlte-input-file required name="employeeCsv" igroup-size="md" placeholder="Choose a cvs file...">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-lightblue">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-file>
                        </div>
                        <div class="form-group col-6">
                            <label for="importFile" class="font-weight-normal"> Employees Images Zip </label>
                            <x-adminlte-input-file required name="employeeImages" igroup-size="md" placeholder="Choose a zip file...">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-lightblue">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input-file>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('employee.index') }}" class="btn btn-danger btn-md">Cancel</a>
                            </div>
                            <div class="col text-right">
                                <button type="submit" class="btn btn-success btn-md">Import</button>
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
@stop
