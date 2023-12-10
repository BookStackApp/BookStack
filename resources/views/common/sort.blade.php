<?php
    $selectedSort = (isset($sort) && array_key_exists($sort, $options)) ? $sort : array_keys($options)[0];
    $order = (isset($order) && in_array($order, ['asc', 'desc'])) ? $order : 'asc';
?>
<div component="list-sort-control" class="list-sort-container">
    <div class="list-sort-label">{{ trans('common.sort') }}</div>
    <form refs="list-sort-control@form"
          @if($useQuery ?? false)
              action="{{ url()->current() }}"
              method="get"
          @else
              action="{{ url("/preferences/change-sort/{$type}") }}"
              method="post"
          @endif
    >
        <input type="hidden" name="_return" value="{{ url()->current() }}">

        @if($useQuery ?? false)
            @foreach(array_filter(request()->except(['sort', 'order'])) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        @else
            {!! method_field('PATCH') !!}
            {!! csrf_field() !!}
        @endif

        <input refs="list-sort-control@sort" type="hidden" value="{{ $selectedSort }}" name="sort">
        <input refs="list-sort-control@order" type="hidden" value="{{ $order }}" name="order">

        <div class="list-sort">
            <div component="dropdown" class="list-sort-type dropdown-container">
                <div refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" aria-label="{{ trans('common.sort_options') }}" tabindex="0">{{ $options[$selectedSort] }}</div>
                <ul refs="dropdown@menu list-sort-control@menu" class="dropdown-menu">
                    @foreach($options as $key => $label)
                        <li @if($key === $selectedSort) class="active" @endif><a href="#" data-sort-value="{{$key}}" class="text-item">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <button class="list-sort-dir" type="button" data-sort-dir
                    aria-label="{{ trans('common.sort_direction_toggle') }} - {{ $order === 'asc' ? trans('common.sort_ascending') : trans('common.sort_descending') }}" tabindex="0">
                @icon($order === 'desc' ? 'sort-up' : 'sort-down')
            </button>
        </div>
    </form>
</div>