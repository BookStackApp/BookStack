@extends('simple-layout')

@section('body')

    <div class="container small pt-xl">

        <main class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.user_api_token_create') }}</h1>

            <form action="{{ $user->getEditUrl('/create-api-token') }}" method="post">
                {!! csrf_field() !!}

                <div class="setting-list">
                    @include('users.api-tokens.form')

                    <div>
                        <p class="text-warn italic">
                            {{ trans('settings.user_api_token_create_secret_message') }}
                        </p>
                    </div>
                </div>

                <div class="form-group text-right">
                    <a href="{{ $user->getEditUrl('#api_tokens') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button" type="submit">{{ trans('common.save') }}</button>
                </div>

            </form>

        </main>
    </div>

@stop
