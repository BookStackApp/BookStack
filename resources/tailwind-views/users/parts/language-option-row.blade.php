{{--
$value - Currently selected lanuage value
--}}
<div class="grid half gap-xl v-center">
    <div>
        <label for="user-language" class="setting-list-label">{{ trans('settings.users_preferred_language') }}</label>
        <p class="small">
            {{ trans('settings.users_preferred_language_desc') }}
        </p>
    </div>
    <div>
        <select name="language" id="user-language">
            @foreach(trans('settings.language_select') as $lang => $label)
                <option @if($value === $lang) selected @endif value="{{ $lang }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>