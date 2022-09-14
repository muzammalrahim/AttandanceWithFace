@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <div class="row">
        <div class="col-3">
            <h1>{{ $content_header }}</h1>
        </div>
        <div class="col-3">
            <select id="department-change" class="form-control department-select selectpicker show-tick bs-select-picker-custom-style" data-live-search="true">
                <option data-icon="fa fa-filter" value="0"> Select Department </option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" data-tokens="{{ \Illuminate\Support\Str::title($department->name) }}">
                        {{ \Illuminate\Support\Str::title($department->name) }} </option>
                @endforeach
            </select>
        </div>
        <div class="col-3">
            @php
            $config = ['format' => 'DD-MM-YYYY', 'daysOfWeekDisabled' => [0]];
            @endphp
            <x-adminlte-input-date id="date-filter" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" name="dateFilter" :config="$config" placeholder="Choose a date...">
                <x-slot name="prependSlot">
                    <div class="input-group-text bg-gradient-gray">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
        </div>
    </div>
@stop

@section('content')
    @include('admin.errors')
    @include('admin.success')
    <table class="table table-bordered table-striped text-center align-middle" id="att-datatable">
        <thead class="bg-dark">
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>CNIC</th>
            <th>Department</th>
            <th>Date</th>
            <th>Time in</th>
            <th>Time out</th>
            <!-- <th>Action</th> -->
        </tr>
        </thead>
    </table>
    <!-- Delete Modal -->
    <x-adminlte-modal id="modal_delete" title="Delete Employee" theme="danger"
                      icon="fas fa-ban" size='md' v-centered static-backdrop scrollable>
        <div class="text-center">
            <p class="m-auto"> This action cannot be undone. </p>
            <p class="m-auto"> Are you sure you wish to continue </p>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button id="delete_action" theme="primary" label="No" data-dismiss="modal"/>
            <x-adminlte-button id="delete_employee" type="submit" theme="danger" label="Yes"/>
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('plugins.TempusDominusBs4', true)

@section('css')
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
@stop

@section('js')
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
        var APP_URL = '{!! url('/') !!}';
        $(function (){
            getDataTableData();
            // delete modal
            this.delete_action = function (id){
                $('#delete_employee').attr('data-id', id);
            }
            $('#delete_employee').click(function (e){
                e.preventDefault();
                const id = $(this).data('id');
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                $.ajax({
                    type: 'POST',
                    url: APP_URL + '/admin/employees/ajax/delete',
                    data: {
                        id: id
                    },
                    success: (resp) => {
                        Swal.fire({
                            position: 'top-end',
                            type: 'success',
                            title: `<small>${resp.message}</small>`,
                            showConfirmButton: false,
                            timer: 3000,
                        });
                        $('#modal_delete').modal('hide');
                        $('#emp-datatable').DataTable().ajax.reload();
                    },
                    error: (resp) => {
                        Swal.fire({
                            position: 'top-end',
                            type: 'error',
                            title: `<small>${resp.responseJSON.message}</small>`,
                            showConfirmButton: false,
                            timer: 3000,
                        });
                    }
                });
            });
            // get datatable data
            function getDataTableData(departmentId = null, date = null){
                $('#att-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    iDisplayLength: 10,
                    order: [[0, 'desc']],
                    ajax: {
                        url: '{!! route('attendance.datatable') !!}',
                        data: function (data){
                            data.departmentId = departmentId;
                            data.date = date;
                        }
                    },
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'name', name: 'name', orderable: false, searchable: false },
                        { data: 'cnic', name: 'cnic', orderable: false, searchable: false },
                        { data: 'department', name: 'department', orderable: false, searchable: false },
                        { data: 'date', name: 'date', orderable: false, searchable: false },
                        { data: 'time_in', name: 'time_in', orderable: false, searchable: false },
                        { data: 'time_out', name: 'time_out', orderable: false, searchable: false },
                        // { data: 'updated_at', name: 'updated_at', searchable: false },
                        // { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    rowCallback: function (row, data){
                        $(row).find('td').addClass('align-middle');
                    }
                });
            }
            $('#department-change').change(function (){
                getDataTableData($(this).val());
            });
            $('#date-filter').on('hide.datetimepicker', ({date}) => {
                var dateChange = date.format('DD-MM-YYYY');
                getDataTableData(null ,dateChange);
            });
        });
    </script>
@stop
