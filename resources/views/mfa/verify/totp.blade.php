<div class="setting-list-label">Mobile App</div>

<p class="small mb-m">
    Enter the code, generated using your mobile app, below:
</p>

<form action="{{ url('/mfa/totp/verify') }}" method="post">
    {{ csrf_field() }}
    <input type="text"
           name="code"
           placeholder="Provide your app generated code here"
           class="input-fill-width {{ $errors->has('code') ? 'neg' : '' }}">
    @if($errors->has('code'))
        <div class="text-neg text-small px-xs">{{ $errors->first('code') }}</div>
    @endif
    <div class="mt-s text-right">
        <button class="button">{{ trans('common.confirm') }}</button>
    </div>
</form>