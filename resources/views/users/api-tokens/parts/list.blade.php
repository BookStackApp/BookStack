<section class="card content-wrap auto-height" id="api_tokens">
    <div class="flex-container-row wrap justify-space-between items-center mb-s">
        <h2 class="list-heading">{{ trans('settings.users_api_tokens') }}</h2>
        <div class="text-right pt-xs">
            @if(userCan('access-api'))
                <a href="{{ url('/api/docs') }}" class="button outline">{{ trans('settings.users_api_tokens_docs') }}</a>
                <a href="{{ url('/api-tokens/' . $user->id . '/create?context=' . $context) }}" class="button outline">{{ trans('settings.users_api_tokens_create') }}</a>
            @endif
        </div>
    </div>
    <p class="text-small text-muted">{{ trans('settings.users_api_tokens_desc') }}</p>
    @if (count($user->apiTokens) > 0)
        <div class="item-list my-m">
            @foreach($user->apiTokens as $token)
                <div class="item-list-row flex-container-row items-center wrap py-xs gap-x-m">
                    <div class="flex px-m py-xs min-width-m">
                        <a href="{{ $token->getUrl("?context={$context}") }}">{{ $token->name }}</a> <br>
                        <span class="small text-muted italic">{{ $token->token_id }}</span>
                    </div>
                    <div class="flex flex-container-row items-center min-width-m">
                        <div class="flex px-m py-xs text-muted">
                            <strong class="text-small">{{ trans('settings.users_api_tokens_expires') }}</strong> <br>
                            {{ $token->expires_at->format('Y-m-d') ?? '' }}
                        </div>
                        <div class="flex px-m py-xs text-right">
                            <a class="button outline small" href="{{ $token->getUrl("?context={$context}") }}">{{ trans('common.edit') }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted italic py-m">{{ trans('settings.users_api_tokens_none') }}</p>
    @endif
</section>