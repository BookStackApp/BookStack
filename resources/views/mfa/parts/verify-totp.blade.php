<div class="setting-list-label">{{ trans('auth.mfa_option_totp_title') }}</div>

<p class="small mb-m">{{ trans('auth.mfa_verify_totp_desc') }}</p>

<form action="{{ url('/mfa/totp/verify') }}" method="post">
    {{ csrf_field() }}
    <input type="text"
           name="code"
           tabindex="0"
           placeholder="{{ trans('auth.mfa_gen_totp_provide_code_here') }}"
           class="input-fill-width {{ $errors->has('code') ? 'neg' : '' }}">
    @if($errors->has('code'))
        <div class="text-neg text-small px-xs">{{ $errors->first('code') }}</div>
    @endif
    <div class="mt-s text-right">
        <button class="button">{{ trans('common.confirm') }}</button>
    </div>
</form>
