@if(Session::has('success'))
    <div class="notification anim pos">
        <i class="zmdi zmdi-mood"></i> <span>{{ Session::get('success') }}</span>
    </div>
@endif

@if(Session::has('error'))
    <div class="notification anim neg stopped">
        <i class="zmdi zmdi-alert-circle"></i> <span>{{ Session::get('error') }}</span>
    </div>
@endif