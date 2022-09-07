@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show p05 alert-autoclose" role="alert">
        {{Session::get('success')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


@if(Session::has('error'))
    <div class="alert alert-success alert-dismissible fade show p05 alert-autoclose" role="alert">
        {{Session::get('error')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
