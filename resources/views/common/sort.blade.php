<?php
    $selectedSort = (isset($sort) && array_key_exists($sort, $options)) ? $sort : array_keys($options)[0];
    $order = (isset($order) && in_array($order, ['asc', 'desc'])) ? $order : 'asc';
?>
<div class="list-sort-container" list-sort-control>
    <div class="list-sort-label">{{ trans('common.sort') }}</div>
    <form action="{{ url("/settings/users/". user()->id ."/change-sort/{$type}") }}" method="post">

        {!! csrf_field() !!}
        {!! method_field('PATCH') !!}
        <input type="hidden" value="{{ $selectedSort }}" name="sort">
        <input type="hidden" value="{{ $order }}" name="order">

        <div class="list-sort">
            <div component="dropdown" class="list-sort-type dropdown-container">
                <div refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" aria-label="{{ trans('common.sort_options') }}" tabindex="0">{{ $options[$selectedSort] }}</div>
                <ul refs="dropdown@menu" class="dropdown-menu">
                    @foreach($options as $key => $label)
                        <li @if($key === $selectedSort) class="active" @endif><a href="#" data-sort-value="{{$key}}" class="text-item">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <button href="#" class="list-sort-dir" type="button" data-sort-dir
                    aria-label="{{ trans('common.sort_direction_toggle') }} - {{ $order === 'asc' ? trans('common.sort_ascending') : trans('common.sort_descending') }}" tabindex="0">
                @icon($order === 'desc' ? 'sort-up' : 'sort-down')
            </button>
        </div>
    </form>
</div>