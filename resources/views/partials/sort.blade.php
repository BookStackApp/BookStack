<?php
    $selectedSort = (isset($sort) && array_key_exists($sort, $options)) ? $sort : array_keys($options)[0];
    $order = (isset($order) && in_array($order, ['asc', 'desc'])) ? $order : 'asc';
?>
<div class="list-sort-container" list-sort-control>
    <div class="list-sort-label">{{ trans('common.sort') }}</div>
    <form action="{{ url("/settings/users/{$currentUser->id}/change-sort/{$type}") }}" method="post">

        {!! csrf_field() !!}
        {!! method_field('PATCH') !!}
        <input type="hidden" value="{{ $selectedSort }}" name="sort">
        <input type="hidden" value="{{ $order }}" name="order">

        <div class="list-sort">
            <div class="list-sort-type dropdown-container" dropdown>
                <div dropdown-toggle>{{ $options[$selectedSort] }}</div>
                <ul class="dropdown-menu">
                    @foreach($options as $key => $label)
                        <li @if($key === $selectedSort) class="active" @endif><a href="#" data-sort-value="{{$key}}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="list-sort-dir" data-sort-dir>
                @icon($order === 'desc' ? 'sort-up' : 'sort-down')
            </div>
        </div>
    </form>
</div>