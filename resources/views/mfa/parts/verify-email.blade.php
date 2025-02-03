
<div class="setting-list-label">{{ trans('auth.mfa_verify_backup_code') }}</div>

<p class="small mb-m">{{ trans('auth.mfa_verify_backup_code_desc') }}</p>

<form action="{{ url('/mfa/email/verify') }}" method="POST">
    {{ csrf_field() }}
    <input type="text"
        name="code"
        aria-labelledby="totp-verify-input-details"
        placeholder="{{ trans('auth.mfa_gen_totp_provide_code_here') }}"
        class="input-fill-width {{ $errors->has('code') ? 'neg' : '' }}">

        @if($errors->has('code'))
        <div class="text-neg text-small px-xs">{{ $errors->first('code') }}</div>
        @endif

        <div class="mt-s text-right">
            <button class="button">{{ trans('common.confirm') }}</button>
        </div>
</form>
