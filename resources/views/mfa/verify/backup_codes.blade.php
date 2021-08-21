<div class="setting-list-label">{{ trans('auth.mfa_verify_backup_code') }}</div>

<p class="small mb-m">{{ trans('auth.mfa_verify_backup_code_desc') }}</p>

<form action="{{ url('/mfa/backup_codes/verify') }}" method="post">
    {{ csrf_field() }}
    <input type="text"
           name="code"
           placeholder="{{ trans('auth.mfa_verify_backup_code_enter_here') }}"
           class="input-fill-width {{ $errors->has('code') ? 'neg' : '' }}">
    @if($errors->has('code'))
        <div class="text-neg text-small px-xs">{{ $errors->first('code') }}</div>
    @endif
    <div class="mt-s text-right">
        <button class="button">{{ trans('common.confirm') }}</button>
    </div>
</form>