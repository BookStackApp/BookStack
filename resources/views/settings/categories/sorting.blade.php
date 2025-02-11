@extends('settings.layout')

@php
    $sortRules = \BookStack\Sorting\SortRule::allByName();
@endphp

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
                        @foreach($sortRules as $set)
                            <option value="{{$set->id}}"
                                    @if(intval(setting('sorting-book-default', '0')) === $set->id) selected @endif
                            >
                                {{ $set->name }}
                            </option>
                        @endforeach
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
        <div class="flex-container-row items-center gap-m">
            <div class="flex">
                <h2 class="list-heading">{{ trans('settings.sorting_rules') }}</h2>
                <p class="text-muted">{{ trans('settings.sorting_rules_desc') }}</p>
            </div>
            <div>
                <a href="{{ url('/settings/sorting/rules/new') }}"
                   class="button outline">{{ trans('settings.sort_rule_create') }}</a>
            </div>
        </div>

        @if(empty($sortRules))
            <p class="italic text-muted">{{ trans('common.no_items') }}</p>
        @else
            <div class="item-list">
                @foreach($sortRules as $rule)
                    @include('settings.sort-rules.parts.sort-rule-list-item', ['rule' => $rule])
                @endforeach
            </div>
        @endif
    </div>
@endsection