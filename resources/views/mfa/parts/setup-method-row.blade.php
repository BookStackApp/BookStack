<div class="grid half gap-xl">
    <div>
        <div class="setting-list-label">{{ trans('auth.mfa_option_' . $method . '_title') }}</div>
        <p class="small">
            {{ trans('auth.mfa_option_' . $method . '_desc') }}
        </p>
    </div>
    <div class="pt-m">
        @if($userMethods->has($method))
            <div class="text-pos">
                @icon('check-circle')
                {{ trans('auth.mfa_setup_configured') }}
            </div>
            <a href="{{ url('/mfa/' . $method . '/generate') }}" class="button outline small">{{ trans('auth.mfa_setup_reconfigure') }}</a>
            <div component="dropdown" class="inline relative">
                <button type="button" refs="dropdown@toggle" class="button outline small">{{ trans('common.remove') }}</button>
                <div refs="dropdown@menu" class="dropdown-menu">
                    <p class="text-neg small px-m mb-xs">{{ trans('auth.mfa_setup_remove_confirmation') }}</p>
                    <form action="{{ url('/mfa/' . $method . '/remove') }}" method="post">
                        {{ csrf_field() }}
                        {{ method_field('delete') }}
                        <button class="text-primary small text-item">{{ trans('common.confirm') }}</button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ url('/mfa/' . $method . '/generate') }}" class="button outline">{{ trans('auth.mfa_setup_action') }}</a>
        @endif
    </div>
</div>