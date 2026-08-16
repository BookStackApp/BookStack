@extends('layouts.simple')

@section('body')

    <div class="container medium my-xl">

        <div class="grid gap-xxl right-focus">

            <div>
                <div class="mb-l">
                    <a href="{{ url('/my-account/interface') }}" class="text-link">@icon('back') {{ trans('preferences.layout_edit_back_to_preferences') }}</a>
                </div>
                <h5>{{ trans('preferences.layout_edit_layouts') }}</h5>
                <nav class="active-link-list in-sidebar">
                    @foreach($namedLocations as $locationOptKey => $locationOptName)
                        <a href="{{ url('/layouts/' . $locationOptKey) }}" class="{{ $locationOptKey === $location ? 'active' : '' }}">{{ $locationOptName }}</a>
                    @endforeach
                </nav>
            </div>

            <div>
                <div class="card content-wrap auto-height">
                    <div class="flex-container-row justify-space-between items-center">
                        <h1 class="list-heading">{{ trans('preferences.layout_edit') }}</h1>
                        <span class="text-muted text-bigger">{{ $locationName }}</span>
                    </div>
                    <p class="small text-muted">{{ trans('preferences.layout_edit_desc') }}</p>

                    <form component="layout-editor" action="{{ url('layouts/' . $location) }}" method="POST">
                        {{ method_field('PUT') }}
                        {{ csrf_field() }}

                        <input refs="layout-editor@input" type="hidden" name="layout" value="">

                        <div class="flex-container-row gap-m">
                            @include('settings.layouts.parts.block-column', ['columnBlocks' => $blocks['left'] ?? [], 'label' => trans('preferences.layout_edit_left'), 'id' => 'left'])
                            @if(isset($blocks['center']))
                                @include('settings.layouts.parts.block-column', ['columnBlocks' => $blocks['center'] ?? [], 'label' => trans('preferences.layout_edit_center'), 'id' => 'center'])
                            @endif
                            @include('settings.layouts.parts.block-column', ['columnBlocks' => $blocks['right'] ?? [], 'label' => trans('preferences.layout_edit_right'), 'id' => 'right'])
                        </div>

                        <div>
                            @include('settings.layouts.parts.block-column', ['columnBlocks' => $blocks['unused'] ?? [], 'label' => trans('preferences.layout_edit_unused'), 'id' => 'unused'])
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ url('/my-account/interface') }}" class="button outline">{{ trans('common.cancel') }}</a>
                            <button type="submit" form="layout-reset-form" class="button outline">{{ trans('preferences.layout_edit_reset_to_defaults') }}</button>
                            <button type="submit" class="button">{{ trans('preferences.layout_edit_save') }}</button>
                        </div>
                    </form>

                    <form id="layout-reset-form" action="{{ url('/layouts/' . $location . '/reset') }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                    </form>
                </div>
            </div>

        </div>

    </div>

@stop
