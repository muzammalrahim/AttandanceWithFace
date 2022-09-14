@extends('adminlte::page')

@section('title', $title)

@section('content_header')
<div class="row">
    <div class="col-6">
        <h1>{{ $content_header }}</h1>
    </div>
</div>
@stop

@section('content')
    <!-- <p>Welcome to Attendance With Face Admin Dashbord</p> -->
    <div class="row">
        <div class="col-md-3">
            <x-adminlte-small-box title="{{ $totalEmployees }}" text="Total Employees" icon="fas fa-eye text-dark"
                                  theme="teal" url="{{ route('employee.index') }}" url-text="View details"/>
        </div>
        <div class="col-md-3 ">
            <x-adminlte-small-box title="{{ $totalDepartments }}" text="Total Departments" icon="fas fa-eye text-dark"
                                  theme="teal" url="{{ route('department.index') }}" url-text="View details"/>
        </div>
        <div class="col-md-3 ">
            <x-adminlte-small-box title="{{ $totalAttendee }}" text="Total Attendee" icon="fas fa-eye text-dark"
                                  theme="teal" url="{{ route('attendance.index') }}" url-text="View details"/>
        </div>
        <div class="col-md-3">
            <x-adminlte-small-box title="{{ $totalKeyAssigned }}" text="Keys Issued" icon="fas fa-key text-dark"
                                  theme="teal" url="{{ route('assignkey.index') }}" url-text="View details"/>
        </div>
        <!-- Bar Chart Start -->
        <div class="col-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Attendance Department Wise</h3>
                        <a href="{{ route('attendance.index') }}">View Report</a>
                    </div>
                </div>
                <div class="card-body">
{{--                    <div class="d-flex">--}}
{{--                        <p class="d-flex flex-column">--}}
{{--                            <span class="text-bold text-lg">$18,230.00</span>--}}
{{--                            <span>Sales Over Time</span>--}}
{{--                        </p>--}}
{{--                        <p class="ml-auto d-flex flex-column text-right">--}}
{{--                    <span class="text-success">--}}
{{--                      <i class="fas fa-arrow-up"></i> 33.1%--}}
{{--                    </span>--}}
{{--                            <span class="text-muted">Since last month</span>--}}
{{--                        </p>--}}
{{--                    </div>--}}
                    <!-- /.d-flex -->

                    <div class="position-relative mb-4">
                        <canvas id="department-stacked-chart" height="400"></canvas>
                    </div>

                    <div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                            <i class="fas fa-square text-success"></i> Present
                        </span>
                        <span>
                            <i class="fas fa-square text-gray"></i> Timed Out
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Donught Chart Start -->
        <div class="col-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Attendance Department Graphic View</h3>
{{--                        <a href="{{ route('attendance.index') }}">View Report</a>--}}
                    </div>
                </div>
                <div class="card-body pb-5">
                {{--                    <div class="d-flex">--}}
                {{--                        <p class="d-flex flex-column">--}}
                {{--                            <span class="text-bold text-lg">$18,230.00</span>--}}
                {{--                            <span>Sales Over Time</span>--}}
                {{--                        </p>--}}
                {{--                        <p class="ml-auto d-flex flex-column text-right">--}}
                {{--                    <span class="text-success">--}}
                {{--                      <i class="fas fa-arrow-up"></i> 33.1%--}}
                {{--                    </span>--}}
                {{--                            <span class="text-muted">Since last month</span>--}}
                {{--                        </p>--}}
                {{--                    </div>--}}
                <!-- /.d-flex -->
                    <div class="position-relative mb-4">
                        <canvas id="department-donought-chart" height="400"></canvas>
                    </div>

{{--                    <div class="d-flex flex-row justify-content-end">--}}
{{--                        <span class="mr-2">--}}
{{--                            <i class="fas fa-square text-success"></i> Present--}}
{{--                        </span>--}}
{{--                        <span>--}}
{{--                            <i class="fas fa-square text-gray"></i> Timed Out--}}
{{--                        </span>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>
@stop
@section('plugins.Chartjs', true)
@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script type="text/javascript">
        var departmentNamesForBarChart = {{ Js::From($departmentNamesForBarChart) }};
        var totalAttendee = {{ Js::From($totalAttendee) }};
        var totalDepartmentPresentEmployees = {{ Js::from($totalDepartmentPresentEmployees) }};
        var totalDepartmentTimedOutEmployees = {{ Js::from($totalDepartmentTimedOutEmployees) }};
    </script>
    <!-- Dashboard Script -->
    <script src="{{ asset('js/dashboard.js') }}"></script>
@stop
