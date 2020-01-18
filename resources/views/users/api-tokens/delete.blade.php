@extends('simple-layout')

@section('body')
    <div class="container small pt-xl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.user_api_token_delete') }}</h1>

            <p>{{ trans('settings.user_api_token_delete_warning', ['tokenName' => $token->name]) }}</p>

            <div class="grid half">
                <p class="text-neg"><strong>{{ trans('settings.user_api_token_delete_confirm') }}</strong></p>
                <div>
                    <form action="{{ $user->getEditUrl('/api-tokens/' . $token->id) }}" method="POST" class="text-right">
                        {!! csrf_field() !!}
                        {!! method_field('delete') !!}

                        <a href="{{ $user->getEditUrl('/api-tokens/' . $token->id) }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button">{{ trans('common.confirm') }}</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@stop
