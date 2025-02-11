@extends('layouts.simple')

@section('body')

    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'settings'])

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.sort_rule_edit') }}</h1>

            <form action="{{ $rule->getUrl() }}" method="POST">
                {{ method_field('PUT') }}
                {{ csrf_field() }}

                @include('settings.sort-rules.parts.form', ['model' => $rule])

                <div class="form-group text-right">
                    <a href="{{ url("/settings/sorting") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('common.save') }}</button>
                </div>
            </form>
        </div>

        <div id="delete" class="card content-wrap auto-height">
            <div class="flex-container-row items-center gap-l">
                <div class="mb-m">
                    <h2 class="list-heading">{{ trans('settings.sort_rule_delete') }}</h2>
                    <p class="text-muted mb-xs">{{ trans('settings.sort_rule_delete_desc') }}</p>
                    @if($errors->has('delete'))
                        @foreach($errors->get('delete') as $error)
                            <p class="text-neg mb-xs">{{ $error }}</p>
                        @endforeach
                    @endif
                </div>
                <div class="flex">
                    <form action="{{ $rule->getUrl() }}" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}

                        @if($errors->has('delete'))
                            <input type="hidden" name="confirm" value="true">
                        @endif

                        <div class="text-right">
                            <button type="submit" class="button outline">{{ trans('common.delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
