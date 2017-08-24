
<div notification="success" data-autohide class="pos" @if(session()->has('success')) data-show @endif>
    <i class="zmdi zmdi-check-circle"></i> <span>{!! nl2br(htmlentities(session()->get('success'))) !!}</span>
</div>

<div notification="warning" class="warning" @if(session()->has('warning')) data-show @endif>
    <i class="zmdi zmdi-info"></i> <span>{!! nl2br(htmlentities(session()->get('warning'))) !!}</span>
</div>

<div notification="error" class="neg" @if(session()->has('error')) data-show @endif>
    <i class="zmdi zmdi-alert-circle"></i> <span>{!! nl2br(htmlentities(session()->get('error'))) !!}</span>
</div>
