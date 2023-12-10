<form action="{{ url('/preferences/toggle-dark-mode') }}" method="post">
    {{ csrf_field() }}
    {{ method_field('patch') }}
    <input type="hidden" name="_return" value="{{ url()->current() }}">
    @if(setting()->getForCurrentUser('dark-mode-enabled'))
        <button class="{{ $classes ?? '' }}"><span>@icon('light-mode')</span><span>{{ trans('common.light_mode') }}</span></button>
    @else
        <button class="{{ $classes ?? '' }}"><span>@icon('dark-mode')</span><span>{{ trans('common.dark_mode') }}</span></button>
    @endif
</form>