<div component="notification"
     option:notification:type="success"
     option:notification:auto-hide="true"
     option:notification:show="{{ session()->has('success') ? 'true' : 'false' }}"
     style="display: none;"
     class="notification pos"
     role="alert">
    @icon('check-circle') <span>{!! nl2br(htmlentities(session()->get('success'))) !!}</span><div class="dismiss">@icon('close')</div>
</div>

<div component="notification"
     option:notification:type="warning"
     option:notification:auto-hide="false"
     option:notification:show="{{ session()->has('warning') ? 'true' : 'false' }}"
     style="display: none;"
     class="notification warning"
     role="alert">
    @icon('info') <span>{!! nl2br(htmlentities(session()->get('warning'))) !!}</span><div class="dismiss">@icon('close')</div>
</div>

<div component="notification"
     option:notification:type="error"
     option:notification:auto-hide="false"
     option:notification:show="{{ session()->has('error') ? 'true' : 'false' }}"
     style="display: none;"
     class="notification neg"
     role="alert">
    @icon('danger') <span>{!! nl2br(htmlentities(session()->get('error'))) !!}</span><div class="dismiss">@icon('close')</div>
</div>