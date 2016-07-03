
<div class="notification anim pos" @if(!session()->has('success')) style="display:none;" @endif>
    <i class="zmdi zmdi-check-circle"></i> <span>{!! nl2br(htmlentities(session()->get('success'))) !!}</span>
</div>

<div class="notification anim warning stopped" @if(!session()->has('warning')) style="display:none;" @endif>
    <i class="zmdi zmdi-info"></i> <span>{!! nl2br(htmlentities(session()->get('warning'))) !!}</span>
</div>

<div class="notification anim neg stopped" @if(!session()->has('error')) style="display:none;" @endif>
    <i class="zmdi zmdi-alert-circle"></i> <span>{!! nl2br(htmlentities(session()->get('error'))) !!}</span>
</div>
