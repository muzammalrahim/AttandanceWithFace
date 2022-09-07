@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1>{{ $content_header }}</h1>
        </div>
        <div class="col-6 text-right">
            <a href="{{ route('employee.create') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus-circle"> Add</i>
            </a>
        </div>
    </div>
@stop

@section('content')
    @include('admin.errors')
    @include('admin.success')
    <table class="table table-bordered table-striped text-center align-middle" id="emp-datatable">
        <thead class="bg-dark">
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>CNIC</th>
            <th>Department</th>
            <th>Photo</th>
            <th>Updated At</th>
            <th>Action</th>
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

@section('js')
    <script type="text/javascript">
        var APP_URL = '{!! url('/') !!}';
        $(function (){
            $('#emp-datatable').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 10,
                order: [[5, 'desc']],
                ajax: {
                    url: '{!! route('employee.datatable') !!}'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'cnic', name: 'cnic' },
                    { data: 'department', name: 'department', orderable: false, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'updated_at', name: 'updated_at', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                rowCallback: function (row, data){
                    $(row).find('td').addClass('align-middle');
                }
            });
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
        });
    </script>
@stop
