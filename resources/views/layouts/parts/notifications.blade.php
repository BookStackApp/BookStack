<div component="notification"
     option:notification:type="success"
     option:notification:auto-hide="true"
     option:notification:show="{{ session()->has('success') ? 'true' : 'false' }}"
     style="display: none;"
     class="notification pos"
     role="alert">
    @icon('check-circle') <span>@if(session()->has('success')){!! nl2br(htmlentities(session()->get('success'))) !!}@endif</span><div class="dismiss">@icon('close')</div>
</div>

<div component="notification"
     option:notification:type="warning"
     option:notification:auto-hide="false"
     option:notification:show="{{ session()->has('warning') ? 'true' : 'false' }}"
     style="display: none;"
     class="notification warning"
     role="alert">
    @icon('info') <span>@if(session()->has('warning')){!! nl2br(htmlentities(session()->get('warning'))) !!}@endif</span><div class="dismiss">@icon('close')</div>
</div>

<div component="notification"
     option:notification:type="error"
     option:notification:auto-hide="false"
     option:notification:show="{{ session()->has('error') ? 'true' : 'false' }}"
     style="display: none;"
     class="notification neg"
     role="alert">
    @icon('danger') <span>@if(session()->has('error')){!! nl2br(htmlentities(session()->get('error'))) !!}@endif</span><div class="dismiss">@icon('close')</div>
</div>