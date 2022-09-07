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
        <form method="POST" action="{{ route('department.store') }}">
            @csrf
            <div class="card card-secondary">
                <div class="card-header">
                    <p class="font-weight-bold"> Add New Department </p>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="name" class="font-weight-normal"> Name </label>
                            <input type="text" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Name">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="address" class="font-weight-normal"> Address </label>
                            <input type="text" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Address">
                            @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <a href="{{ route('department.index') }}" class="btn btn-danger btn-md">Cancel</a>
                            </div>
                            <div class="col text-right">
                                <button type="submit" class="btn btn-success btn-md">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop
