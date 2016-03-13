
<div class="notification anim pos" @if(!Session::has('success')) style="display:none;" @endif>
    <i class="zmdi zmdi-check-circle"></i> <span>{!! nl2br(htmlentities(Session::get('success'))) !!}</span>
</div>

<div class="notification anim warning stopped" @if(!Session::has('warning')) style="display:none;" @endif>
    <i class="zmdi zmdi-info"></i> <span>{!! nl2br(htmlentities(Session::get('warning'))) !!}</span>
</div>

<div class="notification anim neg stopped" @if(!Session::has('error')) style="display:none;" @endif>
    <i class="zmdi zmdi-alert-circle"></i> <span>{!! nl2br(htmlentities(Session::get('error'))) !!}</span>
</div>
