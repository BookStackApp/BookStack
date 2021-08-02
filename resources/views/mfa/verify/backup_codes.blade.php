<div class="setting-list-label">Backup Code</div>

<p class="small mb-m">
    Enter one of your remaining backup codes below:
</p>

<form action="{{ url('/mfa/backup_codes/verify') }}" method="post">
    {{ csrf_field() }}
    <input type="text"
           name="code"
           placeholder="Enter backup code here"
           class="input-fill-width {{ $errors->has('code') ? 'neg' : '' }}">
    @if($errors->has('code'))
        <div class="text-neg text-small px-xs">{{ $errors->first('code') }}</div>
    @endif
    <div class="mt-s text-right">
        <button class="button">{{ trans('common.confirm') }}</button>
    </div>
</form>