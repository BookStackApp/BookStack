
<div class="notification anim pos" @if(!Session::has('success')) style="display:none;" @endif>
    <i class="zmdi zmdi-check-circle"></i> <span>{{ Session::get('success') }}</span>
</div>

<div class="notification anim neg stopped" @if(!Session::has('error')) style="display:none;" @endif>
    <i class="zmdi zmdi-alert-circle"></i> <span>{{ Session::get('error') }}</span>
</div>
