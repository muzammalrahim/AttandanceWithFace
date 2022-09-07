@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1>{{ $content_header }}</h1>
        </div>
        <div class="col-6 text-right">
            <a href="{{ route('department.create') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus-circle"></i> Add
            </a>
        </div>
    </div>
@stop

@section('content')
    @include('admin.errors')
    @include('admin.success')
    <table class="table table-bordered table-striped text-center" id="dep-datatable">
        <thead class="bg-dark">
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Address</th>
            <th>Updated At</th>
            <th>Action</th>
        </tr>
        </thead>
    </table>
    <!-- Edit Model -->
    <x-adminlte-modal id="modal_edit" title="Edit Department" size="lg" theme="primary"
                      icon="fas fa-lg fa-edit" v-centered static-backdrop scrollable>
        <form id="edit-department">
            <x-adminlte-alert class="alert-warning print-error-msg d-none" icon="fa fa-lg fa-exclamation-triangle" title="Warning">
                <ul></ul>
            </x-adminlte-alert>
            <input type="hidden" name="id" value="">
            <div class="row">
                <x-adminlte-input type="text" name="name" label="Name" placeholder="Name"
                                  fgroup-class="col-6" disable-feedback/>
                <x-adminlte-input type="text" name="address" label="Address" placeholder="Address"
                                  fgroup-class="col-6" disable-feedback/>
            </div>
            <x-slot name="footerSlot">
                <x-adminlte-button id="edit_action" class="mr-auto" theme="danger" label="Cancel" data-dismiss="modal"/>
                <x-adminlte-button id="save_department" data-id="" type="submit" theme="success" label="Update"/>
            </x-slot>
        </form>
    </x-adminlte-modal>
    <!-- Delete Modal -->
    <x-adminlte-modal id="modal_delete" title="Delete Department" theme="danger"
                      icon="fas fa-ban" size='md' v-centered static-backdrop scrollable>
        <div class="text-center">
            <p class="m-auto"> This action cannot be undone. </p>
            <p class="m-auto"> All the employee of the department will be deleted. </p>
            <p class="m-auto"> Are you sure you wish to continue </p>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button id="delete_action" theme="primary" label="No" data-dismiss="modal"/>
            <x-adminlte-button id="delete_department" type="submit" theme="danger" label="Yes"/>
        </x-slot>
    </x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('js')
    <script type="text/javascript">
        var APP_URL = '{!! url('/') !!}';
        $(function (){
            $('#dep-datatable').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 10,
                order: [[3, 'desc']],
                ajax: {
                    url: '{!! route('department.datatable') !!}'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'address', name: 'address' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                rowCallback: function (row){
                    row.lastChild.setAttribute('class', 'd-flex justify-content-center');
                }
            });
            // edit modal
            this.edit_action = function (this_el, id){
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                $.ajax({
                   type: 'GET',
                   url: APP_URL+'/admin/departments/ajax/edit/'+id,
                   beforeSend: function () {
                       $('.print-error-msg').addClass('d-none');
                       $('#save_department').attr('disabled', true);
                   },
                   success: function (resp){
                       $.each(resp.data, function (key, item){
                          $('[name='+key+']', '#edit-department').val(item);
                       });
                       $('#save_department').attr('disabled', false);
                   }
                });
            }
            // save edit modal
            $('#save_department').click(function (e){
                e.preventDefault();
                const formData = $('#edit-department').serializeArray();
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                $.ajax({
                    type: 'POST',
                    url: '{!! route('department.update') !!}',
                    data: formData,
                    success: function (resp){
                        Swal.fire({
                            position: 'top-end',
                            type: 'success',
                            title: `<small>${resp.message}</small>`,
                            showConfirmButton: false,
                            timer: 3000,
                        });
                        $('#modal_edit').modal('hide');
                        $('#dep-datatable').DataTable().ajax.reload();
                    },
                    error: function (resp){
                        if (resp.status === 400){
                            $('.print-error-msg').find('ul').html('').removeClass('d-none');
                            $.each(resp.responseJSON.errors, function (key, error){
                                $('.print-error-msg').find('ul').append('<li>'+ error +'</li>');
                            });
                        } else {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: '<small>'+resp.responseJSON.message+'</small>',
                                showConfirmButton: true,
                            });
                        }
                    }
                });
            });
            // delete modal
            this.delete_action = function (id){
                $('#delete_department').data('id', id);
            }
            $('#delete_department').click(function (e){
                e.preventDefault();
                const id = $(this).data('id');
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                $.ajax({
                    type: 'POST',
                    url: APP_URL + '/admin/departments/ajax/delete',
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
                        $('#dep-datatable').DataTable().ajax.reload();
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
