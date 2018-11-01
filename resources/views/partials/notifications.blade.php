<div notification="success" style="display: none;" data-autohide class="pos" @if(session()->has('success')) data-show @endif>
    @icon('check-circle') <span>{!! nl2br(htmlentities(session()->get('success'))) !!}</span>
</div>

<div notification="warning" style="display: none;" class="warning" @if(session()->has('warning')) data-show @endif>
    @icon('info') <span>{!! nl2br(htmlentities(session()->get('warning'))) !!}</span>
</div>

<div notification="error" style="display: none;" class="neg" @if(session()->has('error')) data-show @endif>
    @icon('danger') <span>{!! nl2br(htmlentities(session()->get('error'))) !!}</span>
</div>
