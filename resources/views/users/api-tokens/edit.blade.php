@extends('simple-layout')

@section('body')

    <div class="container small pt-xl">

        <main class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.user_api_token') }}</h1>

            <form action="{{ $user->getEditUrl('/api-tokens/' . $token->id) }}" method="post">
                {!! method_field('put') !!}
                {!! csrf_field() !!}

                <div class="setting-list">

                    <div class="grid half gap-xl v-center">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.user_api_token_client_id') }}</label>
                            <p class="small">{{ trans('settings.user_api_token_client_id_desc') }}</p>
                        </div>
                        <div>
                            @include('form.text', ['name' => 'client_id', 'readonly' => true])
                        </div>
                    </div>


                    @if( $secret )
                        <div class="grid half gap-xl v-center">
                            <div>
                                <label class="setting-list-label">{{ trans('settings.user_api_token_client_secret') }}</label>
                                <p class="small text-warn">{{ trans('settings.user_api_token_client_secret_desc') }}</p>
                            </div>
                            <div>
                                <input type="text" readonly="readonly" value="{{ $secret }}">
                            </div>
                        </div>
                    @endif

                    @include('users.api-tokens.form', ['model' => $token])
                </div>

                <div class="grid half gap-xl v-center">

                    <div class="text-muted text-small">
                        <span title="{{ $token->created_at }}">
                            {{ trans('settings.user_api_token_created', ['timeAgo' => $token->created_at->diffForHumans()]) }}
                        </span>
                        <br>
                        <span title="{{ $token->updated_at }}">
                            {{ trans('settings.user_api_token_updated', ['timeAgo' => $token->created_at->diffForHumans()]) }}
                        </span>
                    </div>

                    <div class="form-group text-right">
                        <a href="{{  $user->getEditUrl('#api_tokens') }}" class="button outline">{{ trans('common.back') }}</a>
                        <a href="{{  $user->getEditUrl('/api-tokens/' . $token->id . '/delete') }}" class="button outline">{{ trans('settings.user_api_token_delete') }}</a>
                        <button class="button" type="submit">{{ trans('common.save') }}</button>
                    </div>
                </div>

            </form>

        </main>
    </div>

@stop
