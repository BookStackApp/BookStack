@extends('settings.layout')

@section('card')
    <h1 id="registration" class="list-heading">{{ trans('settings.reg_settings') }}</h1>
    <form action="{{ url("/settings/registration") }}" method="POST">
        {!! csrf_field() !!}
        <input type="hidden" name="section" value="registration">

        <div class="setting-list">
            <div class="grid half gap-xl">
                <div>
                    <label class="setting-list-label">{{ trans('settings.reg_enable') }}</label>
                    <p class="small">{!! trans('settings.reg_enable_desc') !!}</p>
                </div>
                <div>
                    @include('form.toggle-switch', [
                        'name' => 'setting-registration-enabled',
                        'value' => setting('registration-enabled'),
                        'label' => trans('settings.reg_enable_toggle')
                    ])

                    @if(in_array(config('auth.method'), ['ldap', 'saml2', 'oidc']))
                        <div class="text-warn text-small mb-l">{{ trans('settings.reg_enable_external_warning') }}</div>
                    @endif

                    <label for="setting-registration-role">{{ trans('settings.reg_default_role') }}</label>
                    <select id="setting-registration-role" name="setting-registration-role" @if($errors->has('setting-registration-role')) class="neg" @endif>
                        <option value="0" @if(intval(setting('registration-role', '0')) === 0) selected @endif>-- {{ trans('common.none') }} --</option>
                        @foreach(\BookStack\Auth\Role::all() as $role)
                            <option value="{{$role->id}}"
                                    data-system-role-name="{{ $role->system_name ?? '' }}"
                                    @if(intval(setting('registration-role', '0')) === $role->id) selected @endif
                            >
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid half gap-xl">
                <div>
                    <label for="setting-registration-restrict" class="setting-list-label">{{ trans('settings.reg_confirm_restrict_domain') }}</label>
                    <p class="small">{!! trans('settings.reg_confirm_restrict_domain_desc') !!}</p>
                </div>
                <div class="pt-xs">
                    <input type="text" id="setting-registration-restrict" name="setting-registration-restrict" placeholder="{{ trans('settings.reg_confirm_restrict_domain_placeholder') }}" value="{{ setting('registration-restrict', '') }}">
                </div>
            </div>

            <div class="grid half gap-xl">
                <div>
                    <label class="setting-list-label">{{ trans('settings.reg_email_confirmation') }}</label>
                    <p class="small">{{ trans('settings.reg_confirm_email_desc') }}</p>
                </div>
                <div>
                    @include('form.toggle-switch', [
                        'name' => 'setting-registration-confirmation',
                        'value' => setting('registration-confirmation'),
                        'label' => trans('settings.reg_email_confirmation_toggle')
                    ])
                </div>
            </div>

        </div>

        <div class="form-group text-right">
            <button type="submit" class="button">{{ trans('settings.settings_save') }}</button>
        </div>
    </form>
@endsection