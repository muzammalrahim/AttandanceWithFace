@extends('adminlte::page')

{{--@section('title', $title)--}}

@section('content_header')

{{--    <h1> {{ $content_header }} </h1>--}}

@stop

@section('content')
    <p>Welcome to Attendance With Face Admin Dashbord</p>
    <div class="row">
      
        <div class="col-md-3">
    {{-- Themes --}}
    <x-adminlte-small-box title="65" text="Total Employees" icon="fas fa-eye text-dark"
        theme="teal" url="#" url-text="View details"/>

   </div>
   <div class="col-md-3 ">
    {{-- Themes --}}
    <x-adminlte-small-box title="60" text="ON TIME PERCENTAGE" icon="fas fa-eye text-dark"
        theme="teal" url="#" url-text="View details"/>

   </div>
   <div class="col-md-3 ">
    {{-- Themes --}}
    <x-adminlte-small-box title="55" text="ON TIME TODAY" icon="fas fa-eye text-dark"
        theme="teal" url="#" url-text="View details"/>

   </div>
   <div class="col-md-3">
  <x-adminlte-small-box title="10" text="LATE TODAY" icon="fas fa-eye text-dark"
        theme="teal" url="#" url-text="View details"/>

    </div>
    </div>
@push('js')
<script>

    $(document).ready(function() {

        let sBox = new _AdminLTE_SmallBox('sbUpdatable');

        let updateBox = () =>
        {
            // Stop loading animation.
            sBox.toggleLoading();

            // Update data.
            let rep = Math.floor(1000 * Math.random());
            let idx = rep < 100 ? 0 : (rep > 500 ? 2 : 1);
            let text = 'Reputation - ' + ['Basic', 'Silver', 'Gold'][idx];
            let icon = 'fas fa-medal ' + ['text-primary', 'text-light', 'text-warning'][idx];
            let url = ['url1', 'url2', 'url3'][idx];

            let data = {text, title: rep, icon, url};
            sBox.update(data);
        };

        let startUpdateProcedure = () =>
        {
            // Simulate loading procedure.
            sBox.toggleLoading();

            // Wait and update the data.
            setTimeout(updateBox, 2000);
        };

        setInterval(startUpdateProcedure, 10000);
    })

</script>
@endpush
@stop
@section('')
@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    {{-- <script> console.log('Hi!'); </script> --}}
@stop
