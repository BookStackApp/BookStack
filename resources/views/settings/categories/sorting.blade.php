@extends('settings.layout')

@section('card')
    <h1 id="sorting" class="list-heading">{{ trans('settings.sorting') }}</h1>
    <form action="{{ url("/settings/sorting") }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="section" value="sorting">

        <div class="setting-list">
            <div class="grid half gap-xl items-center">
                <div>
                    <label for="setting-sorting-book-default"
                           class="setting-list-label">{{ trans('settings.sorting_book_default') }}</label>
                    <p class="small">{{ trans('settings.sorting_book_default_desc') }}</p>
                </div>
                <div>
                    <select id="setting-sorting-book-default" name="setting-sorting-book-default"
                            @if($errors->has('setting-sorting-book-default')) class="neg" @endif>
                        <option value="0" @if(intval(setting('sorting-book-default', '0')) === 0) selected @endif>
                            -- {{ trans('common.none') }} --
                        </option>
{{--                        TODO--}}
{{--                        @foreach(\BookStack\Users\Models\Role::all() as $role)--}}
{{--                            <option value="{{$role->id}}"--}}
{{--                                    data-system-role-name="{{ $role->system_name ?? '' }}"--}}
{{--                                    @if(intval(setting('registration-role', '0')) === $role->id) selected @endif--}}
{{--                            >--}}
{{--                                {{ $role->display_name }}--}}
{{--                            </option>--}}
{{--                        @endforeach--}}
                    </select>
                </div>
            </div>

        </div>

        <div class="form-group text-right">
            <button type="submit" class="button">{{ trans('settings.settings_save') }}</button>
        </div>
    </form>
@endsection

@section('after-card')
    <div class="card content-wrap auto-height">
        <h2 class="list-heading">{{ trans('settings.sorting_sets') }}</h2>
        <p class="text-muted">{{ trans('settings.sorting_sets_desc') }}</p>
{{--        TODO--}}
    </div>
@endsection