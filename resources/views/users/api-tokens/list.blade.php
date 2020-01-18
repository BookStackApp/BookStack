<section class="card content-wrap auto-height" id="api_tokens">
    <div class="grid half mb-s">
        <div><h2 class="list-heading">{{ trans('settings.users_api_tokens') }}</h2></div>
        <div class="text-right pt-xs">
            @if(userCan('access-api'))
                <a href="{{ url('/api/docs') }}" class="button outline">{{ trans('settings.users_api_tokens_docs') }}</a>
                <a href="{{ $user->getEditUrl('/create-api-token') }}" class="button outline">{{ trans('settings.users_api_tokens_create') }}</a>
            @endif
        </div>
    </div>
    @if (count($user->apiTokens) > 0)
        <table class="table">
            <tr>
                <th>{{ trans('common.name') }}</th>
                <th>{{ trans('settings.users_api_tokens_expires') }}</th>
                <th></th>
            </tr>
            @foreach($user->apiTokens as $token)
                <tr>
                    <td>
                        {{ $token->name }} <br>
                        <span class="small text-muted italic">{{ $token->token_id }}</span>
                    </td>
                    <td>{{ $token->expires_at->format('Y-m-d') ?? '' }}</td>
                    <td class="text-right">
                        <a class="button outline small" href="{{ $user->getEditUrl('/api-tokens/' . $token->id) }}">{{ trans('common.edit') }}</a>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="text-muted italic py-m">{{ trans('settings.users_api_tokens_none') }}</p>
    @endif
</section>